<?php

namespace App\Jobs;

use App\Http\Services\Analytics\DailyAnalyticsService;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class PaymentsAnalyticsDailySingleJob extends BaseJob
{

   public function __construct(
      private Carbon $theDate)
   {}

   public function handle(DailyAnalyticsService $dailyAnalyticsService)
   {
      $dailyAnalyticsService->generate($this->theDate);
   }

}