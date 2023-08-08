<?php

namespace App\Jobs;

use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use App\Http\BillPay\Services\SMS\SMSService;
use App\Jobs\BaseJob;

class SendSMSNotificationsJob extends BaseJob
{

   private $arrSMSes;
   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(Array $arrSMSes)
   {
      $this->arrSMSes = $arrSMSes;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(SMSClientBinderService $smsClientBinderService, 
                                 SMSService $smsService)
   {
      //Bind the SMS Client
         $smsClient = '';
         if(!$smsClient && (\env('SMS_SEND_USE_MOCK') == "YES")){
               $smsClient = 'MockDeliverySMS';
         }
         if(!$smsClient && \config('efectivo_clients.'.$this->momoDTO->urlPrefix.'.hasOwnSMS')){
               $smsClient = \strtoupper($this->momoDTO->urlPrefix).'SMS';
         }
         if(!$smsClient){
               $smsClient = \env('SMPP_CHANNEL');
         }
         $smsClientBinderService->bind($smsClient);
      //
      $smsService->sendMany($this->arrSMSes);
   }

}
