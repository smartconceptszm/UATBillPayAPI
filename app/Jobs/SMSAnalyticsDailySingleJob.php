<?php

namespace App\Jobs;

use App\Http\Services\SMS\Generators\SMSDailyAnalyticsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class SMSAnalyticsDailySingleJob extends BaseJob
{

   public $timeout = 180;

   public function __construct(
      private Carbon $theDate)
   {}

   public function handle(SMSDailyAnalyticsService $dailyAnalyticsService)
   {
      $dailyAnalyticsService->generate($this->theDate);
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}