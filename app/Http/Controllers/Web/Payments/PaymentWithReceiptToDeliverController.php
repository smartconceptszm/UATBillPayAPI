<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentWithReceiptToDeliverService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentWithReceiptToDeliverController extends Controller
{

	public function __construct(
		private PaymentWithReceiptToDeliverService $paymentWithReceiptToDeliverService)
	{}

   public function update($id){

      try {
         $this->response['data'] = $this->paymentWithReceiptToDeliverService->update($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
