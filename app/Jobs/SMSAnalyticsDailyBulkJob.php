<?php

namespace App\Jobs;

use App\Jobs\SMSAnalyticsDailySingleJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class SMSAnalyticsDailyBulkJob extends BaseJob
{

   public function __construct(
      private string $theDate)
   {}

   public function handle()
   {

      $theDate = Carbon::parse($this->theDate);
      $dayOfMonth = $theDate->copy()->startOfMonth();
      $endOfMonth = $theDate->endOfMonth();
      $daysDone = 0;
      while ($dayOfMonth <= $endOfMonth) {
         SMSAnalyticsDailySingleJob::dispatch($dayOfMonth)
                                    ->delay(Carbon::now()->addSeconds($daysDone * 3))
                                    ->onQueue('UATlow');
         $daysDone += 1;
         $dayOfMonth->addDay();
      }
      Log::info('(SCL) '.$daysDone. ' Daily SMS analytics Jobs dispatched for the month: '.$endOfMonth->format('Y-F'));

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}
