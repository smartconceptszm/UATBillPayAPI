<?php

namespace App\Http\Services\USSD\PayBusLevy;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayBusLevy_Step_5 
{

   public function run(BaseDTO $txDTO)
   {
      if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
         try {
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
         } catch (\Throwable $e) {
            $txDTO->error = 'Bus levy step 5. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;        
   }

}