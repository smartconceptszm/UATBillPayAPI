<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentsByConsumerTierService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentsByConsumerTierController extends Controller
{

	public function __construct(
		private PaymentsByConsumerTierService $paymentsByConsumerTierService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
            $this->response['data'] =  $this->paymentsByConsumerTierService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   public function summary(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentsByConsumerTierService->summary($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }



}
