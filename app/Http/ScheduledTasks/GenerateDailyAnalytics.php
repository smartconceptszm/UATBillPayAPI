<?php
namespace App\Http\ScheduledTasks;

use App\Http\Services\Analytics\DailyAnalyticsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class GenerateDailyAnalytics 
{

	public function __construct(
		private DailyAnalyticsService $dailyAnalyticsService) 	
	{}

	public function __invoke()
	{

		try {
			Log::info("Scheduled task (Generate Daily Analytics) invoked");
			$this->dailyAnalyticsService->generate(Carbon::yesterday());
		} catch (\Exception $e) {
			Log::error("In Generate Daily Analytics, scheduled task: " . $e->getMessage());
		}

	}

}
