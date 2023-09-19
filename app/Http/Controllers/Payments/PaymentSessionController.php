<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentSessionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSessionController extends Controller
{

	public function __construct(
		private PaymentSessionService $theService)
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
