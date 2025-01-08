<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\PaymentRequestService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DTOs\CardDTO;

class PaymentViaCardController extends Controller
{

   private $validationRules = [
                  //'mobileNumber' => 'required|string|size:12',
                  'customerAccount' => 'required|string',
                  'paymentAmount' => 'required|string',
                  'wallet_id' => 'required|string',
                  'menu_id' => 'required|string'
               ];
   public function __construct(
      private PaymentRequestService $paymentRequestService,
      private CardDTO $cardDTO)
   {}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $params = $this->getParameters($request);
         $params['channel'] = 'WEBSITE';
         $this->response['data'] = $this->paymentRequestService->initiateCardWebPayement($params,$this->cardDTO);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request)
   {

      try {
         $this->response['data'] = $this->paymentRequestService->confirmWebPayment($request->input('id'));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   
}
