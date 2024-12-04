<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\ReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{

   private $validationRules = [  
                              'id' => 'required|string',
                           ];

	public function __construct(
		private ReceiptService $receiptService)
	{}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->receiptService->create($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->receiptService->update($this->getParameters($request),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
