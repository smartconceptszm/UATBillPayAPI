<?php

namespace App\Http\Services\USSD\VacuumTankerSwasco;

use App\Http\DTOs\BaseDTO;

class VacuumTankerSwasco_Step_6
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