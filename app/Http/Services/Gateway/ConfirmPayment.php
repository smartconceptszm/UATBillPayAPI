<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ConfirmPayment
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function handle(BaseDTO $paymentDTO)
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

         //Bind Receipting Handler
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            $receiptingHandler = $theMenu->receiptingHandler;
            $billingClient = $theMenu->billingClient;
            if ($billpaySettings['USE_RECEIPTING_MOCK_'.strtoupper($paymentDTO->urlPrefix)] == "YES"){
               $receiptingHandler = "MockReceipting";
               $billingClient = "MockBillingClient";
            }
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            App::bind(\App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
         //

         
         $paymentDTO =  App::make(Pipeline::class)
               ->send($paymentDTO)
               ->through(
                  [
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_DispatchReConfirmationJob::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                     \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                     \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                     \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class 
                  ]
               )
               ->thenReturn();
      } catch (\Throwable $e) {
         $paymentDTO->error='At get confirm payment pipeline. '.$e->getMessage();
         Log::info($paymentDTO->error);
      }

      return $paymentDTO;

   }

}
