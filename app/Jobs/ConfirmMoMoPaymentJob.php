<?php

namespace App\Jobs;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\MoMo\ConfirmMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmMoMoPaymentJob extends BaseJob
{

   // public $timeout = 600;
   private $momoDTO;

   public function __construct(BaseDTO $momoDTO)
   {
      $this->momoDTO = $momoDTO;
   }

   public function handle(ConfirmMoMoPayment $confirmMoMoPayment,
      ClientMenuService $clientMenuService)
   {

      //Bind the MoMoClient
         $momoClient = $this->momoDTO->mnoName;
         if(\env("MOBILEMONEY_USE_MOCK") == 'YES'){
            $momoClient = 'MoMoMock';
         }
         App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,$momoClient);
      //
      
      //Bind billing related services
         $theMenu = $clientMenuService->findById($this->momoDTO->menu_id);
         //Bind the billing client
            $this->momoDTO->billingClient = $theMenu->billingClient; 
            $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$this->momoDTO->billingClient;
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
         //
         //Bind Receipting Handler
            $receiptingHandler = $theMenu->receiptingHandler;
            if (\env('USE_RECEIPTING_MOCK') == "YES"){
               $receiptingHandler = "MockReceipting";
            }
            App::bind(\App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment::class,$receiptingHandler);
         //
      //
      
      //Bind the SMS Client
         $smsClient = '';
         if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClient = 'MockSMSDelivery';
         }
         if(!$smsClient && \env($this->momoDTO->mnoName.'_HAS_FREESMS') == "YES"){
               $smsClient = $this->momoDTO->mnoName.'DeliverySMS';
         }
         if(!$smsClient && (\env(\strtoupper($this->momoDTO->urlPrefix).'_HAS_OWNSMS') == 'YES')){
               $smsClient = \strtoupper($this->momoDTO->urlPrefix).'SMS';
         }
         if(!$smsClient){
               $smsClient = \env('SMPP_CHANNEL');
         }
         App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClient);
      //
      //Handle Job Service
         $confirmMoMoPayment->handle($this->momoDTO);
      //

   }

}