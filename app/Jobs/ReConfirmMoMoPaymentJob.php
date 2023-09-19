<?php

namespace App\Jobs;

use App\Http\Services\External\BillingClients\BillingClientBinderService;
use App\Http\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\Services\External\SMSClients\SMSClientBinderService;
use App\Http\Services\MoMo\ReConfirmMoMoPayment;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ReConfirmMoMoPaymentJob extends BaseJob
{

   public function __construct(private BaseDTO $momoDTO)
   {}

   public function handle(ReConfirmMoMoPayment $reConfirmMoMoPayment, 
      BillingClientBinderService $billingClientBinderService,
      MoMoClientBinderService $moMoClientBinderService,
      SMSClientBinderService $smsClientBinderService)
   {
      //Bind the billing client
         $billingClientBinderService->bind($this->momoDTO->urlPrefix);
      //Bind the MoMoClient
         $momoClient = $this->momoDTO->mnoName;
         if(\env("MOBILEMONEY_USE_MOCK") == 'YES'){
            $momoClient = 'MoMoMock';
         }
         $moMoClientBinderService->bind($momoClient);
      //Bind the SMS Client
         $smsClient = '';
         if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClient = 'MockDeliverySMS';
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
         $smsClientBinderService->bind($smsClient);
      //
      //Handle Job Service
      $reConfirmMoMoPayment->handle($this->txDTO);
   }
    
}