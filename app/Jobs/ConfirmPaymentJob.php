<?php

namespace App\Jobs;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmPaymentJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ConfirmPayment $confirmPayment,
      ClientMenuService $clientMenuService)
   {

      //Bind the PaymentsProvider Client Wallet 
         $walletHandler = $this->paymentDTO->walletHandler;
         if(\env("WALLET_USE_MOCK") == 'YES'){
            $walletHandler = 'MockWallet';
         }
         App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
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

      //SMS handling
         if(\env($this->paymentDTO->walletHandler.'_HAS_FREESMS') == "YES"){
            Cache::put($this->paymentDTO->transactionId.'_smsClient',
                           $this->paymentDTO->walletHandler.'DeliverySMS',Carbon::now()->addSeconds(2));
         }
      //
      //Handle Job Service
         return $confirmPayment->handle($this->paymentDTO);
      //

   }

}