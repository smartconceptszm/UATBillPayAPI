<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\SMSDashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSDashboardController extends Controller
{

	public function __construct(
		private SMSDashboardService $theService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->theService->findAll($request->all());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
