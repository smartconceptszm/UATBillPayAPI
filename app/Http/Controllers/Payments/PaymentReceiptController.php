<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentReceiptService;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{

   private $validationRules = [
                              'id' => 'required|string',
                           ];
   private $theService;
   public function __construct(PaymentReceiptService $theService)
   {
      $this->theService = $theService;
   }


   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->theService->create($request->all());
      } catch (\Exception $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
            $this->response['data'] = $this->theService->update($request->all(),$id);
      } catch (\Exception $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
