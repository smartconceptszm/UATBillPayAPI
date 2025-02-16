<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentSubmittedService
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private PaymentToReviewService $paymentToReviewService,
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function findAll(array $criteria){
      try {
         $dto=(object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m-d',$dto->theMonth.'-01');
         $dateFrom = $theDate->copy()->startOfDay();
         $dayOfRun = Carbon::now();
         if ($theDate->isSameMonth($dayOfRun)) {
            $dateTo = $dayOfRun->copy()->subMinutes(30);
         } else {
            $dateTo = $theDate->copy()->endOfMonth();
         }
         $dateFrom = $dateFrom->format('Y-m-d H:i:s');
         $dateTo = $dateTo->format('Y-m-d H:i:s');
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('clients as c','cw.client_id','=','c.id')
                        ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')

                        ->join('sessions as s','p.session_id','=','s.id')
                        ->join('mnos','s.mno_id','=','mnos.id')

                        ->join('client_menus as m','p.menu_id','=','m.id')

                  ->select('s.id as session_id','s.sessionId','s.client_id','s.customerJourney','s.mobileNumber',
                              's.customerAccount','s.revenuePoint','s.response','s.status','s.created_at','p.id',
                              'p.reference','p.ppTransactionId','p.surchargeAmount','p.paymentAmount',
                              'p.receiptAmount','p.transactionId','p.receiptNumber','p.tokenNumber',
                              'p.receipt','p.channel','p.paymentStatus','p.error','s.menu_id',
                              'm.description as paymentType','mnos.name as mno','s.mno_id',
                              'c.shortName','c.name as clientName','pps.shortName as paymentProvider');

         if($dateFrom && $dateTo){
            $records =$records->where('p.created_at', '>=' ,$dateFrom)
                              ->where('p.created_at', '<=', $dateTo);
         }
         $records = $records->where('p.paymentStatus', '=',PaymentStatusEnum::Submitted->value)
                              //->where('cw.client_id', '=', $dto->client_id)
                              ->orderByDesc('p.created_at');

         return $records->get();

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function update(string $id):string{

      try {
         
         $thePayment = $this->paymentToReviewService->findById($id);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         if($user = Auth::user()){
            $paymentDTO->user_id = $user->id;
         } 
         $paymentDTO->error = "";
         $logMessage = "";
         //Reconfirm/Review the payment Transaction
            $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($paymentDTO->payments_provider_id);
            if($paymentsProviderCredentials['TRANSACTION_CAN_BE_RECONFIRMED'] == 'YES'){
               $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
               $paymentDTO = $this->confirmPayment->handle($paymentDTO);
               if($paymentDTO->error==''){
                  $logMessage = $paymentDTO->receipt;
               }else{
                  $logMessage = $paymentDTO->error ;
               }
            }else{
               $logMessage =  "Payments via ". $paymentDTO->walletHandler.' cannot be reviewed';
            }
         //

         return $logMessage;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
