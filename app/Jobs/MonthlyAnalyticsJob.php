<?php

namespace App\Jobs;

use App\Http\Services\Analytics\MonthlyAnalyticsService;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class MonthlyAnalyticsJob extends BaseJob
{

   public function __construct(
      private string $theDate)
   {}

   public function handle(MonthlyAnalyticsService $monthlyAnalyticsService)
   {
      $theDate = Carbon::parse($this->theDate);
      $monthlyAnalyticsService->generate($theDate);
   }

}