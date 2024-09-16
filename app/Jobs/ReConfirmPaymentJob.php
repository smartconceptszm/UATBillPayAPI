<?php

namespace App\Jobs;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Gateway\ReConfirmPayment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ReConfirmPaymentJob extends BaseJob
{

   // public $timeout = 600;

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ReConfirmPayment $reConfirmPayment, 
      ClientMenuService $clientMenuService)
   {

      //Bind the PaymentsProviderClient
         $paymentsProviderClient = $this->paymentDTO->walletHandler;
         if(\env("WALLET_USE_MOCK") == 'YES'){
            $paymentsProviderClient = 'MockWallet';
         }
         App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$paymentsProviderClient);
      //

      //Bind Receipting Handler
         $theMenu = $clientMenuService->findById($this->paymentDTO->menu_id);
         $receiptingHandler = $theMenu->receiptingHandler;
         $billingClient = $theMenu->billingClient;
         if (\env('USE_RECEIPTING_MOCK') == "YES"){
            $receiptingHandler = "MockReceipting";
            $billingClient = "MockBillingClient";
         }
         App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
         App::bind(\App\Http\Services\External\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
      //
      
      //Handle Job Service
         Log::info('('.$this->paymentDTO->urlPrefix.') Reconfirmation job launched. Transaction ID = '.$this->paymentDTO->transactionId.
                     '- Channel: '.$this->paymentDTO->channel.' - Wallet: '.$this->paymentDTO->walletNumber);
                     
         $reConfirmPayment->handle($this->paymentDTO);
         
   }
    
}