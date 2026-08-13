<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\BatchReceiptingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BatchReceiptingController extends Controller
{

   private  $validationRules = [
                                 'receiptingType' => 'required|string',
                                 'client_id' => 'required|string',
                                 'dateFrom' => 'required|string',
                                 'dateTo' => 'required|string'
                              ];
                              
	public function __construct(
		private BatchReceiptingService $batchReceiptingService)
	{}

   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $response = $this->batchReceiptingService->create($this->getParameters($request));
         $this->response['data'] = $response->data;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
