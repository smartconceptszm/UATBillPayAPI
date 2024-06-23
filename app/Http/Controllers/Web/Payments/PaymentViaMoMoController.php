<?php

namespace App\Http\Controllers\Web\Payments;

use App\Http\Services\Web\Payments\PaymentRequestService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DTOs\MoMoDTO;

class PaymentViaMoMoController extends Controller
{

   private $validationRules = [
                  'payments_provider_id' => 'required',
                  'mobileNumber' => 'required|string',
                  'walletNumber' => 'required|string',
                  'paymentAmount' => 'required',
                  'client_id' => 'required',
                  'menu_id' => 'required',
               ];
   public function __construct(
      private PaymentRequestService $paymentRequestService,
      private MoMoDTO $moMoDTO)
   {}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
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
