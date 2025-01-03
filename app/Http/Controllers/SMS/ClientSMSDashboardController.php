<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\ClientSMSDashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientSMSDashboardController extends Controller
{

	public function __construct(
		private ClientSMSDashboardService $clientSMSDashboardService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->clientSMSDashboardService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
