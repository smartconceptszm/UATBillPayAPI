<?php

namespace App\Http\Controllers\PublicAPI;

use App\Http\Services\PublicAPI\PaymentSessionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSessionController extends Controller
{

   private $validationRules = [
                  'mobileNumber' => 'required|string|size:12',
                  'customerAccount' => 'required|string',
                  'walletNumber' => 'required|string',
                  'paymentAmount' => 'required|string',
                  'menu_id' => 'required|string',
               ];

   public function store(Request $request, PaymentSessionService $paymentSessionService)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $params = $this->getParameters($request);
         $params['customerAccount'] = strtoupper($params['customerAccount']);
         $this->response['data'] = $paymentSessionService->handle($params);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   
}
