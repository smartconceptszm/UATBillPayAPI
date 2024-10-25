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
      $startOfMonth = $theDate->copy()->startOfMonth(); 
      $endOfMonth = $theDate->endOfMonth();
      $daysDone = 0;
      while ($startOfMonth <= $endOfMonth) {
         Queue::later(Carbon::now()->addSeconds($daysDone*5),new PaymentsAnalyticsDailySingleJob($startOfMonth),'','high');
         $daysDone += 1;
         $startOfMonth->addDay();
      }
      Log::info('(SCL) '.$daysDone. ' Daily transaction analytics Jobs dispatched for the month: '.$endOfMonth->format('Y-F'));
      
   }

}