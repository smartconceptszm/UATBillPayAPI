<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Services\Gateway\PaymentRequestService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DTOs\MoMoDTO;

class PaymentViaMoMoController extends Controller
{

   private $validationRules = [
                  'mobileNumber' => 'required|string|size:12',
                  'walletNumber' => 'required|string|size:12',
                  'customerAccount' => 'required|string',
                  'paymentAmount' => 'required|string',
                  'wallet_id' => 'required|string',
                  'menu_id' => 'required|string',
               ];
   public function __construct(
      private PaymentRequestService $paymentRequestService,
      private MoMoDTO $moMoDTO)
   {}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $params = $this->getParameters($request);
         $this->validate($request, $this->validationRules);
         $params = $this->getParameters($request);
         $params['channel'] = 'WEBSITE';
         $this->response['data'] = $this->paymentRequestService->initiateWebPayement($params,$this->moMoDTO);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   
}
