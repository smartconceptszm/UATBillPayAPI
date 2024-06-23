<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{

   private $validationRules = [
                              'id' => 'required|string',
                           ];
	public function __construct(
		private PaymentReceiptService $paymentReceiptService)
	{}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->paymentReceiptService->create($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->paymentReceiptService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
