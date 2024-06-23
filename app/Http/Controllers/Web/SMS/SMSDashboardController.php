<?php

namespace App\Http\Controllers\Web\SMS;

use App\Http\Services\Web\SMS\SMSDashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSDashboardController extends Controller
{

	public function __construct(
		private SMSDashboardService $smsDashboardService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->smsDashboardService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
