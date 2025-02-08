<?php

namespace App\Jobs;

use App\Http\Services\SMS\SMSService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\SMSTxDTO;
use App\Jobs\BaseJob;

class SendSMSesJob extends BaseJob
{

   // public $timeout = 600;
   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(private Array $arrSMSes)
   {}

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(SMSService $smsService, SMSTxDTO $smsTxDTO)
   {

      foreach ($this->arrSMSes as $smsData) {
         $smsTxDTO = $smsService->send($smsTxDTO->fromArray($smsData));
         if($smsData['type'] != "RECEIPT"){
            Log::info('('.$smsTxDTO->urlPrefix.') '.
                        'SMS Message Dispatched: Phone: '.
                        $smsData['mobileNumber'].' - :Type '.
                        $smsData['type'].' - Message: '.
                        $smsData['message']
                     );
         }
      }

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}
