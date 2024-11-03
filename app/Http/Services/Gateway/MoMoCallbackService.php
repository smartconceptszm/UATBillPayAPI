<?php

namespace App\Http\Services\Gateway;



use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;
use Exception;

class MoMoCallbackService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private MoMoDTO $paymentDTO)
   {}

   public function handleAirtel(array $callbackParams):string{
      
      try {
         
         $thePayment = $this->paymentToReviewService->findByTransactionId($callbackParams['id']);
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         $paymentDTO->ppTransactionId = $callbackParams['airtel_money_id'];
         if($callbackParams['status_code'] == 'TS'){
            //Bind Receipting Handler
               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
               $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
               $receiptingHandler = $theMenu->receiptingHandler;
               $billingClient = $theMenu->billingClient;
               if ($billpaySettings['USE_RECEIPTING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
                  $receiptingHandler = "MockReceipting";
                  $billingClient = "MockBillingClient";
               }
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
               App::bind(\App\Http\Services\External\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
            //
            
            $paymentDTO->paymentStatus = 'PAID | NOT RECEIPTED';
            $paymentDTO->error = "";
            $paymentDTO =  App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through(
                              [
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                                 \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                                 \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                                 \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class 
                                 
                              ]
                           )
                           ->thenReturn();
         }else{
            $paymentDTO->error = $callbackParams['message'];
            $paymentDTO->paymentStatus = 'PAYMENT FAILED';
            $paymentDTO =  App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through(
                              [
                                 \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                                 \App\Http\Services\Gateway\Utility\Step_LogStatus::class 
                              ]
                           )
                           ->thenReturn();
         }
         
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return "Handled";
      
   }

}
