<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentWithReceiptToDeliverService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentWithReceiptToDeliverController extends Controller
{

	public function __construct(
		private PaymentWithReceiptToDeliverService $theService)
	{}

   public function update(Request $request,$id){
      try {
         $this->response['data'] = $this->theService->update($request->all(),$id );
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);
   }

}
