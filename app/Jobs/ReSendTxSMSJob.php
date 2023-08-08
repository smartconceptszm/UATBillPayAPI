<?php

namespace App\Jobs;

use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use App\Http\BillPay\Services\MoMo\ReSendTxSMS;
use App\Http\BillPay\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ReSendTxSMSJob extends BaseJob
{

   private $momoDTO;
   public function __construct(BaseDTO $momoDTO)
   {
      $this->momoDTO = $momoDTO;
   }

   public function handle(SMSClientBinderService $smsClientBinderService, 
         ReSendTxSMS $reSendService)
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
      $reSendService->handle($this->momoDTO);
   }

}
