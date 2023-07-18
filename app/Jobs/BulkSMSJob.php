<?php

namespace App\Jobs;

use App\Http\BillPay\Services\SMS\SMSService;
use App\Jobs\BaseJob;

class BulkSMSJob extends BaseJob
{

   private $arrSMSes;
   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(Array $arrSMSes)
   {
      $this->arrSMSes=$arrSMSes;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(SMSService $smsService)
   {
      $smsService->sendMany($this->arrSMSes);
   }

}
