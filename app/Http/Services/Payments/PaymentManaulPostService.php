<?php

namespace App\Http\Services\Payments;

use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient;
use App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS;
use App\Http\Services\MoMo\Utility\Step_UpdateTransaction;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\MoMo\Utility\Step_LogStatus;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class PaymentManaulPostService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private MoMoDTO $momoDTO)
   {}

   public function create(array $data):object|null{
      try {
         $thePayment = $this->paymentToReviewService->findById($data['id']);
         $momoDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         if($momoDTO->receiptNumber != ''){
            throw new Exception("Payment appears receipted! Receipt number ".$momoDTO->receiptNumber);
         }
         if($momoDTO->mnoTransactionId != ''){
            if($momoDTO->mnoTransactionId == $data['mnoTransactionId']){
               throw new Exception($momoDTO->mnoName ." transaction Id ".$data['mnoTransactionId']. " already captured.");
            }
         }  

         //Bind Receipting Handler
            $theMenu = $this->clientMenuService->findById($momoDTO->menu_id);
            $theMenu = \is_null($theMenu)?null: (object)$theMenu->toArray();
            $receiptingHandler = $theMenu->receiptingHandler;
            if (\env('USE_RECEIPTING_MOCK') == "YES"){
               $receiptingHandler = "MockReceipting";
            }
            App::bind(\App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //

         //Bind the SMS Clients
            $smsClientKey = '';
            if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClientKey = 'MockSMSDelivery';
            }
            if(!$smsClientKey && (\env(\strtoupper($momoDTO->urlPrefix).'_HAS_OWNSMS') == 'YES')){
               $smsClientKey = \strtoupper($momoDTO->urlPrefix).'SMS';
            }
            if(!$smsClientKey){
               $smsClientKey = \env('SMPP_CHANNEL');
            }
            App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
         //
         
         $user = Auth::user(); 
         $momoDTO->user_id = $user->id;
         $momoDTO->mnoTransactionId = $data['mnoTransactionId'];
         $momoDTO->error = "";
         $momoDTO = App::make(Pipeline::class)
                  ->send($momoDTO)
                  ->through(
                     [
                        Step_PostPaymentToClient::class,
                        Step_SendReceiptViaSMS::class,
                        Step_UpdateTransaction::class,  
                        Step_LogStatus::class 
                     ]
                  )
                  ->thenReturn();
         if($momoDTO->error==''){
            $response = (object)[
                     'data' => $momoDTO->receipt   
               ];
         }else{
            $response = (object)[
               'data' => $momoDTO->error   
         ];
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

}
