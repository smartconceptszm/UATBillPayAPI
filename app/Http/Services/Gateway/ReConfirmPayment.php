<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ReConfirmPayment
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private ConfirmPayment $confirmPayment)
   {}

   public function handle(BaseDTO $paymentDTO): BaseDTO
   {

      try {

         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         //Bind the PaymentsProviderClient
            $walletHandler = $paymentDTO->walletHandler;
            if($billpaySettings['WALLET_USE_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == 'YES'){
               $walletHandler = 'MockWallet';
            }
            App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
         //

         //Bind Receipting and Billing Handlers
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
               $receiptingHandler = "MockReceipting";
            }
            if ($billpaySettings['USE_BILLING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //

         for ($i = 0; $i < (int)$billpaySettings['PAYMENT_REVIEW_THRESHOLD']; $i++) {

            $paymentDTO->error = '';
            $paymentDTO->status = "REVIEWED";
            $paymentDTO =  App::make(Pipeline::class)
                           ->send($paymentDTO)
                           ->through(
                              [
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                                 \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                                 \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                                 \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                                 \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class 
                              ]
                           )
                           ->thenReturn();

            if($paymentDTO->paymentStatus == PaymentStatusEnum::Payment_Failed->value){
               if(!(
                     (\strpos($paymentDTO->error,'on get transaction status'))
                     || (\strpos($paymentDTO->error,'Token error'))
                     || (\strpos($paymentDTO->error,"Status Code"))
                     || (\strpos($paymentDTO->error,"on collect funds"))
                  ))
               {
                  break;
               }
            }else{
               break;
            }
         }
         
      } catch (\Throwable $e){
         Log::error("At re-confirm payment job. " . $e->getMessage() . ' - Session: ' . $paymentDTO->sessionId);
      }
      return $paymentDTO;
      
   }

}
