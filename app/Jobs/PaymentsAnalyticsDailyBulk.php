<?php

namespace App\Jobs;

use App\Jobs\PaymentsAnalyticsDailySingle;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\BaseJob;

class PaymentsAnalyticsDailyBulk extends BaseJob
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
         Queue::later(Carbon::now()->addSeconds(1),new PaymentsAnalyticsDailySingle($startOfMonth));
         $daysDone += 1;
         $startOfMonth->addDay();
      }
      Log::info('(SCL) '.$daysDone. ' Daily transaction analytics jobs dispatched for the month: '.$endOfMonth->format('Y-F'));
      
   }

}