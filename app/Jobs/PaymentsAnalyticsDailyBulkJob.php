<?php

namespace App\Jobs;

use App\Jobs\PaymentsAnalyticsDailySingleJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class PaymentsAnalyticsDailyBulkJob extends BaseJob
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
         Queue::later(Carbon::now()->addSeconds($daysDone*30),new PaymentsAnalyticsDailySingleJob($dayOfMonth),'','high');
         $daysDone += 1;
         $dayOfMonth->addDay();
      }
      Log::info('(SCL) '.$daysDone. ' Daily transaction analytics Jobs dispatched for the month: '.$endOfMonth->format('Y-F'));
      
   }

}