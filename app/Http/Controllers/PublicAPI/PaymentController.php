<?php

namespace App\Http\Controllers\PublicAPI;


use App\Http\Services\PublicAPI\IInitiateAPIPayment;
use App\Http\Services\PublicAPI\PaymentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

   private $validationRules = [
                  'payment_id' => 'required|string'
               ];

   public function store(Request $request, IInitiateAPIPayment $initiateAPIPayment)
   {

      try {
          
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $params = $this->getParameters($request);
         $this->response['data'] = $initiateAPIPayment->handle($params);
 
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function show(PaymentService $paymentService,string $id)
   {

      try {
         $this->response['data'] = $paymentService->findById($id);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

   
}
