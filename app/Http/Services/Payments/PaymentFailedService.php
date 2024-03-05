<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentFailedService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService,
      private ClientMenuService $clientMenuService,
      private MoMoDTO $momoDTO)
   {}

   public function findAll(array $criteria=null):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                  ->join('sessions as s','p.session_id','=','s.id')
                  ->join('mnos','p.mno_id','=','mnos.id')
                  ->join('client_menus as m','p.menu_id','=','m.id')
                  ->select('p.id','p.created_at','p.mobileNumber','p.accountNumber','p.receiptNumber',
                           'p.receiptAmount','p.paymentAmount','p.transactionId','p.district',
                           'p.mnoTransactionId','mnos.name as mno','m.prompt as paymentType',
                           'p.paymentStatus','p.error');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at',[$dto->dateFrom, $dto->dateTo]);
         }
         $allFailed = $records->where('p.client_id', '=', $dto->client_id)
                              ->whereIn('p.paymentStatus', ['SUBMISSION FAILED','PAYMENT FAILED'])
                              ->orderByDesc('p.created_at');
         $allFailed = $records->get();
         $providerErrors = $allFailed->filter(
               function ($item) {
                  if (
                        ((\strpos($item->error,"Status Code")) || (\strpos($item->error,"on get transaction status")) 
                           || (\strpos($item->error,"Token error")) || (\strpos($item->error,"on collect funds")))
                        ||
                        ($item->paymentStatus == "SUBMISSION FAILED")   
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
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->error = "";

         //Bind the MoMoClient
            $momoClient = $momoDTO->mnoName;
            if(\env("MOBILEMONEY_USE_MOCK") == 'YES'){
               $momoClient = 'MoMoMock';
            }
            App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,$momoClient);
         //

         //Bind the billing client
            $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$momoDTO->billingClient;
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
         //
   
         //Bind Receipting Handler
            $theMenu = $this->clientMenuService->findById($momoDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            if (\env('USE_RECEIPTING_MOCK') == "YES"){
               $receiptingHandler = "MockReceipting";
            }
            App::bind(\App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment::class,$receiptingHandler);
         //
   
         //Bind the SMS Client
            $smsClient = '';
            if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
                  $smsClient = 'MockSMSDelivery';
            }
            if(!$smsClient && \env($momoDTO->mnoName.'_HAS_FREESMS') == "YES"){
                  $smsClient = $momoDTO->mnoName.'DeliverySMS';
            }
            if(!$smsClient && (\env(\strtoupper($momoDTO->urlPrefix).'_HAS_OWNSMS') == 'YES')){
                  $smsClient = \strtoupper($momoDTO->urlPrefix).'SMS';
            }
            if(!$smsClient){
                  $smsClient = \env('SMPP_CHANNEL');
            }
            App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClient);
         //
         
         //Reconfirm/Review the payment Transaction
            $momoDTO =  App::make(Pipeline::class)
                  ->send($momoDTO)
                  ->through(
                     [
                        \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                        \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                        \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                        \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                        \App\Http\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                        \App\Http\Services\MoMo\Utility\Step_LogStatus::class 
                     ]
                  )
                  ->thenReturn();
            if($momoDTO->error==''){
               $logMessage = $momoDTO->receipt;
            }else{
               $logMessage = $momoDTO->error;
            }
            return $logMessage;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
