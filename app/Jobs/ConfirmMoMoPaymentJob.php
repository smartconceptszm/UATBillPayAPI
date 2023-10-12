<?php

namespace App\Jobs;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\MoMo\ConfirmMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmMoMoPaymentJob extends BaseJob
{

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
      //Bind the billing client
         App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$this->momoDTO->urlPrefix);
      //
      //Bind Receipting Handler
         $theMenu = $clientMenuService->findById($this->momoDTO->menu_id);
         $receiptHandler = $theMenu->paymentHandler;
         if (\env('USE_RECEIPTING_MOCK') == "YES"){
            $receiptHandler = "MockReceipting";
         }
         App::bind(\App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment::class,$receiptHandler);
      //
      //Bind the SMS Client
         $smsClient = '';
         if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClient = 'MockSMSDelivery';
         }
         if(!$smsClient && \env($this->momoDTO->mnoName.'_HAS_FREESMS') == "YES"){
               $smsClient = $this->momoDTO->mnoName.'DeliverySMS';
         }
         if(!$smsClient && \config('efectivo_clients.'.$this->momoDTO->urlPrefix.'.hasOwnSMS')){
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