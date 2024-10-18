<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\DailyAnalyticsService;
use App\Jobs\PaymentsAnalyticsDailyBulk;
use Illuminate\Support\Facades\Queue;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DailyAnalyticsController extends Controller
{

	public function __construct(
		private DailyAnalyticsService $dailyAnalyticsService)
	{}

   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentTransactionService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function generate(Request $request)
   {

      try {
         Queue::later(Carbon::now()->addSeconds(1),new PaymentsAnalyticsDailyBulk($request->input('date')),'','high');
         $this->response['data'] = ['Message' => "Job submitted"];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
