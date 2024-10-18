<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentNotReceiptedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentNotReceiptedController extends Controller
{

	public function __construct(
		private PaymentNotReceiptedService $paymentNotReceiptedService)
	{}

   /**
    * Display a listing of the resource.
    */
   public function index(Request $request)
   {

      try {
         $this->response['data']= $this->paymentNotReceiptedService->findAll($request->query());
      } catch (\Throwable $e) {
         $this->response['status']['code']=500;
         $this->response['status']['message']=$e->getMessage();
      }
      return response()->json( $this->response);

   }

}
