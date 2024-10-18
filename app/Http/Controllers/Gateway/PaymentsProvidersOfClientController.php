<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\PaymentsProvidersOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsProvidersOfClientController extends Controller
{
   
	public function __construct(
		private PaymentsProvidersOfClientService $paymentsProvidersOfClientService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] = $this->paymentsProvidersOfClientService->findAll($request->input('client_id'));
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }
   
}
