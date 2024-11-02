<?php

namespace App\Http\Controllers\Analytics;

use App\Jobs\PaymentsAnalyticsDailySingleJob;
use App\Jobs\PaymentsAnalyticsDailyBulkJob;
use Illuminate\Support\Facades\Queue;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DailyAnalyticsController extends Controller
{

   public function oneMonth(Request $request)
   {

      try {
         Queue::later(Carbon::now()->addSeconds(1),new PaymentsAnalyticsDailyBulkJob($request->input('date')),'','high');
         $this->response['data'] = ['Message' => "Job submitted"];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function oneDay(Request $request)
   {

      try {
         $theDate = Carbon::parse($request->input('date'));
         Queue::later(Carbon::now()->addSeconds(1),new PaymentsAnalyticsDailySingleJob($theDate),'','high');
         $this->response['data'] = ['Message' => "Job submitted"];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
