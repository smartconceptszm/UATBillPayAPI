<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\MainSMSDashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainSMSDashboardController extends Controller
{

	public function __construct(
		private MainSMSDashboardService $mainSMSDashboardService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->mainSMSDashboardService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
