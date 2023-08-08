<?php

namespace App\Jobs;

use App\Http\BillPay\Services\External\BillingClients\BillingClientBinderService;
use App\Http\BillPay\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use App\Http\BillPay\Services\MoMo\ConfirmMoMoPayment;
use App\Http\BillPay\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmMoMoPaymentJob extends BaseJob
{

   private $momoDTO;
   public function __construct(BaseDTO $momoDTO)
   {
      $this->momoDTO = $momoDTO;
   }

   public function handle(ConfirmMoMoPayment $confirmMoMoPayment, 
      BillingClientBinderService $billingClientBinderService,
      MoMoClientBinderService $moMoClientBinderService,
      SMSClientBinderService $smsClientBinderService)
   {
      //Bind the billing client
         $billingClientBinderService->bind($this->momoDTO->urlPrefix);
      //
      //Bind the MoMoClient
         $momoClient = $this->momoDTO->mnoName;
         if(\env("MOBILEMONEY_USE_MOCK") == 'YES'){
            $momoClient = 'MoMoMock';
         }
         $moMoClientBinderService->bind($momoClient);
      //
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
         $confirmMoMoPayment->handle($this->momoDTO);
      //
   }

}