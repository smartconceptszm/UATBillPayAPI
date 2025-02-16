<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedService
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private PaymentToReviewService $paymentToReviewService,
      private ConfirmPayment $confirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function findAll(array $criteria):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')

                  ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                  ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                  ->join('clients as c','cw.client_id','=','c.id')

                  ->join('sessions as s','p.session_id','=','s.id')

                  ->join('client_menus as m','p.menu_id','=','m.id')

                  ->select('p.*','m.prompt as paymentType','pp.shortName as paymentProvider');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->where('p.created_at', '>=', $dto->dateFrom)
                              ->where('p.created_at', '<=',$dto->dateTo);
         }
         $allFailed = $records->whereIn('p.paymentStatus', [PaymentStatusEnum::Submission_Failed->value,PaymentStatusEnum::Payment_Failed->value])
                              ->where('c.id', '=', $dto->client_id)
                              ->orderByDesc('p.created_at');
         $allFailed = $records->get();
         $providerErrors = $allFailed->filter(
               function ($item) {
                  if (
                        ((\strpos($item->error,"Status Code")) || (\strpos($item->error,"on get transaction status")) 
                           || (\strpos($item->error,"Token error")) || (\strpos($item->error,"on collect funds")))
                        ||
                        ($item->paymentStatus == PaymentStatusEnum::Payment_Failed->value)   
                     ) 
                  {
                        return $item;
                  }
               }
            )->values();
         return [
               'all' => $allFailed,
               'providerErrors' => $providerErrors,
            ];

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
               $paymentDTO = $this->confirmPayment->handle($paymentDTO);
               if($paymentDTO->error==''){
                  $logMessage = $paymentDTO->receipt;
               }else{
                  $logMessage = $paymentDTO->error ;
               }
            }else{
               $logMessage =  "Payments via ". $paymentDTO->walletHandler.' cannot be reviewed';
            }


         //x


         return $logMessage;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
