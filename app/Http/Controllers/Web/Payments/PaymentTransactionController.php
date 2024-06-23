<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentTransactionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{

	public function __construct(
		private PaymentTransactionService $paymentTransactionService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->paymentTransactionService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
