<?php

namespace App\Http\Controllers\Analytics;

use App\Jobs\PaymentsAnalyticsDailySingleJob;
use App\Jobs\PaymentsAnalyticsDailyBulkJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DailyAnalyticsController extends Controller
{

   public function oneMonth(Request $request)
   {

      try {
         PaymentsAnalyticsDailyBulkJob::dispatch($request->input('date'))
                                       ->delay(Carbon::now()->addSeconds(1))
                                       ->onQueue('high');

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
         PaymentsAnalyticsDailySingleJob::dispatch($theDate)
                                          ->delay(Carbon::now()->addSeconds(1))
                                          ->onQueue('high');
         $this->response['data'] = ['Message' => "Job submitted"];
         
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
