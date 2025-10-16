<?php

namespace App\Http\Controllers\SMS;

use App\Jobs\SMSAnalyticsDailySingleJob;
use App\Jobs\SMSAnalyticsDailyBulkJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class SMSAdhocAnalyticsController extends Controller
{

   public function oneMonth(Request $request)
   {

      try {
         SMSAnalyticsDailyBulkJob::dispatch($request->input('date'))
                                       ->delay(Carbon::now()->addSeconds(1))
                                       ->onQueue('UATlow');

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
         SMSAnalyticsDailySingleJob::dispatch($theDate)
                                          ->delay(Carbon::now()->addSeconds(1))
                                          ->onQueue('UATlow');
         $this->response['data'] = ['Message' => "Job submitted"];

      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
