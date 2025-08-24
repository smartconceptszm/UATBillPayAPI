<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\CompositePaymentReceiptFailedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompositePaymentReceiptFailedController extends Controller
{

   public function __construct(
		private CompositePaymentReceiptFailedService $compositePaymentReceiptFailedService)
	{}

   /**
    * Update the specified resource in storage.
   */
   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->compositePaymentReceiptFailedService->update($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
