<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentSubmittedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSubmittedController extends Controller
{

	public function __construct(
		private PaymentSubmittedService $paymentSubmittedService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentSubmittedService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $response = $this->paymentSubmittedService->update($id);
         $this->response['data'] = $response;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
