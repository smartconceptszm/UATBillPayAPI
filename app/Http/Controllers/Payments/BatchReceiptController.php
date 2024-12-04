<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\BatchReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BatchReceiptController extends Controller
{

   private  $validationRules = [
                                 'dateFrom' => 'required|string',
                                 'dateTo' => 'required|string'
                              ];
	public function __construct(
		private BatchReceiptService $batchReceiptService)
	{}

   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->validationRules);
         $response = $this->batchReceiptService->create($this->getParameters($request));
         $this->response['data'] = $response->data;
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
