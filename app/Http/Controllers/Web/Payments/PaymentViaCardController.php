<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentRequestService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DTOs\CardDTO;

class PaymentViaCardController extends Controller
{

   private $validationRules = [
                  'mobileNumber' => 'required|string|size:12',
                  'walletNumber' => 'required|string|size:16',
                  'cardHolderName' => 'required',
                  'paymentAmount' => 'required',
                  'cardExpiry' => 'required',
                  'wallet_id' => 'required',
                  'cardCVV' => 'required',
                  'menu_id' => 'required'
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
         $this->response['data'] = $this->paymentRequestService->initiateWebPayement($params,$this->cardDTO);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   
}
