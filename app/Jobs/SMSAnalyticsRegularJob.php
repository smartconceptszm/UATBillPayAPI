<?php

namespace App\Jobs;

use App\Http\Services\SMS\Generators\SMSRegularAnalyticsService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class SMSAnalyticsRegularJob extends BaseJob
{

   public $timeout = 120;

   public function __construct(
      private BaseDTO $smsDTO)
   {}

   public function handle(SMSRegularAnalyticsService $regularAnalyticsService)
   {
      $regularAnalyticsService->generate($this->smsDTO);
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}