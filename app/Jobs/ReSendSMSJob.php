<?php

namespace App\Jobs;

use App\Http\BillPayServices\SMS\SMSRequestHandler;
use App\Jobs\BaseJob;

class ReSendSMSJob extends BaseJob
{

   private $smsParams;
   public function __construct(Array $smsParams)
   {
      $this->smsParams = $smsParams;
   }

   public function handle(SMSRequestHandler $smsService)
   {
      $smsService->reSendSMS($this->smsParams);
   }

}
