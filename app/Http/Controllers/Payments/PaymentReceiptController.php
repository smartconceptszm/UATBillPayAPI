<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\PaymentReceiptService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{

   private $validationRules = [
                              'id' => 'required|string',
                           ];
	public function __construct(
		private PaymentReceiptService $theService)
	{}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->create($request->all());
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $this->response['data'] = $this->theService->update($request->all(),$id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
