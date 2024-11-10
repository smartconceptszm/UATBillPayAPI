<?php

namespace App\Jobs;

use App\Http\Services\Analytics\DailyAnalyticsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class GenerateCloseOfDayAnalyticsJob extends BaseJob
{

   public function handle(DailyAnalyticsService $dailyAnalyticsService)
   {

		try {
			Log::info("(SCL) The Scheduled task: Generate Daily Analytics was invoked");
			$dailyAnalyticsService->generate(Carbon::yesterday());
		} catch (\Exception $e) {
			Log::error("In Generate Daily Analytics, scheduled task: " . $e->getMessage());
		}
      
   }

}