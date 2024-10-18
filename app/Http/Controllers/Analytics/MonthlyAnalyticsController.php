<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Services\Analytics\MonthlyAnalyticsService;
use App\Http\Controllers\Controller;
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
         $analyticsGenerated = $this->monthlyAnalyticsService->generate(Carbon::parse($request->input('date')));
         if($analyticsGenerated ){
            $this->response['data'] = ['Message' => "Monthly analytics generated"];
         }else{
            throw new Exception("Monthly analytics NOT generated", 1);
            
         }
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
