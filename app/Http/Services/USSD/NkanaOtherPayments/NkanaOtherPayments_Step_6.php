<?php

namespace App\Http\Services\USSD\NkanaOtherPayments;

use App\Http\DTOs\BaseDTO;

class NkanaOtherPayments_Step_6
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