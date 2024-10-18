<?php
namespace App\Http\ScheduledTasks;

use App\Http\Services\Analytics\MonthlyAnalyticsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class GenerateMonthlyAnalytics 
{

	public function __construct(
		private MonthlyAnalyticsService $monthlyAnalyticsService) 	
	{}

	public function __invoke()
	{

		try {
			Log::info("Scheduled task (Generate Daily Analytics) invoked");
			$this->monthlyAnalyticsService->generate(Carbon::yesterday());
		} catch (\Exception $e) {
			Log::error("In Generate Daily Analytics, scheduled task: " . $e->getMessage());
		}

	}

}
