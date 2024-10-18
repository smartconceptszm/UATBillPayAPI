<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentFailedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentFailedController extends Controller
{

	public function __construct(
		private PaymentFailedService $paymentFailedService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentFailedService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $response = $this->paymentFailedService->update($id);
         $this->response['data'] = $response;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
