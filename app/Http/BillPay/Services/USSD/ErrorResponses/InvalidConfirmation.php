<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class InvalidConfirmation implements IErrorResponse
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {    
         $txDTO->response = "Invalid selection. Please enter\n". 
                              "1. to Confirm payment\n".
                              "0. Back";
         $cacheValue = \json_encode([
                  'must'=>false,
                  'steps'=>1,
               ]);
         Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
                     Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
      } catch (\Throwable $e) {
         $txDTO->error = 'At Generate invalid input response. '.$e->getMessage();
      }
      return $txDTO;
      
   }
}