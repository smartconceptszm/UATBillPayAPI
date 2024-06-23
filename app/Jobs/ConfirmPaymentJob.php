<?php

namespace App\Jobs;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmPaymentJob extends BaseJob
{

   public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ConfirmPayment $confirmPayment,
      BillingCredentialService $billingCredentialService,
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
         if (\env('USE_RECEIPTING_MOCK') == "YES"){
            $receiptingHandler = "MockReceipting";
         }
         App::bind(\App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment::class,$receiptingHandler);
      //
      
      //Bind the SMS Client
         $billingCredentials = $billingCredentialService->getClientCredentials($this->paymentDTO->client_id);
         $smsClient = '';
         if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClient = 'MockSMSDelivery';
         }
         if(!$smsClient && \env($this->paymentDTO->walletHandler.'_HAS_FREESMS') == "YES"){
               $smsClient = $this->paymentDTO->walletHandler.'DeliverySMS';
         }
         if(!$smsClient && ($billingCredentials['HAS_OWNSMS'] == 'YES')){
               $smsClient = \strtoupper($this->paymentDTO->urlPrefix).'SMS';
         }
         if(!$smsClient){
               $smsClient = \env('SMPP_CHANNEL');
         }
         App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClient);
      //
      //Handle Job Service
         $confirmPayment->handle($this->paymentDTO);
      //

   }

}