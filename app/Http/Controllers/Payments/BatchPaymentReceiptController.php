<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\BatchPaymentReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BatchPaymentReceiptController extends Controller
{

   private  $validationRules = [
                                 'dateFrom' => 'required|string',
                                 'dateTo' => 'required|string'
                              ];
	public function __construct(
		private BatchPaymentReceiptService $theService)
	{}

   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $response = $this->theService->create($this->getParameters($request));
         $this->response['data'] = $response->data;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
