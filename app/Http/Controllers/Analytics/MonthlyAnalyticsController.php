<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\MonthlyAnalyticsService;
use Illuminate\Support\Facades\Queue;
use App\Http\Controllers\Controller;
use App\Jobs\MonthlyAnalyticsJob;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Exception;

class MonthlyAnalyticsController extends Controller
{

	public function __construct(
		private MonthlyAnalyticsService $monthlyAnalyticsService)
	{}

   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->monthlyAnalyticsService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function generate(Request $request)
   {

      try {
         Queue::later(Carbon::now()->addSeconds(1),new MonthlyAnalyticsJob($request->input('date')),'','high');
         $this->response['data'] = ['Message' => "Monthly Analytics Job dispatched"];
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
