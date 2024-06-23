<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentFailedBatchService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentFailedBatchController extends Controller
{

   private $validationRules = [
                              'dateFrom' => 'required|string',
                              'dateTo' => 'required|string',
                           ];
	public function __construct(
		private PaymentFailedBatchService $paymentFailedBatchService)
	{}


   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $newBatch = $this->paymentFailedBatchService->create($this->getParameters($request));
         $this->response['data'] = $newBatch->data;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
