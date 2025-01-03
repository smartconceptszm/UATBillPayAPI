<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Jobs\PaymentsAnalyticsDailySingleJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DailyAnalytics extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
            $yesterday = Carbon::yesterday()->toDateString();
            $lastDailyAnalytics = Cache::get('DATE_OF_LAST_DAILY_ANALYTICS');
            if($lastDailyAnalytics  != $yesterday ){
               Cache::put('DATE_OF_LAST_DAILY_ANALYTICS',$yesterday,Carbon::now()->addHours(30));
               PaymentsAnalyticsDailySingleJob::dispatch(Carbon::yesterday())
                                                ->delay(Carbon::now()->addSeconds(1))
                                                ->onQueue('high');
            }
      } catch (\Throwable $e) {
         $paymentDTO->error='At refreshing analytics. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}