<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class InvalidConfirmation implements IErrorResponse
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {   
         $txDTO->subscriberInput = $txDTO->customerJourney;
         $txDTO->customerJourney = '';
         $txDTO->response = "Invalid selection. Please enter\n". 
                              "1. to Confirm payment\n".
                              "0. Back";
         $cacheValue = \json_encode([
                  'must'=>false,
                  'steps'=>1,
               ]);
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
                     Carbon::now()->addMinutes(intval($billpaySettings['SESSION_CACHE'])));
      } catch (\Throwable $e) {
         $txDTO->error = 'At Generate invalid confirmation response. '.$e->getMessage();
      }
      return $txDTO;
      
   }
}