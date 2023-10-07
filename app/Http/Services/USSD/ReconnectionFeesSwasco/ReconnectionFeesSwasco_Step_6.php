<?php

namespace App\Http\Services\USSD\ReconnectionFeesSwasco;

use App\Http\DTOs\BaseDTO;

class ReconnectionFeesSwasco_Step_6
{

   public function run(BaseDTO $txDTO)
   {
      if (\count(\explode("*", $txDTO->customerJourney)) > 5) {
         $txDTO->error = "Duplicated request from ".$txDTO->mnoName.
                                 " with input: ".$txDTO->subscriberInput; 
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;        
   }

}