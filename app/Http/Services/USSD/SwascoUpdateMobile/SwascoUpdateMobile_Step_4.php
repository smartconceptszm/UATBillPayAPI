<?php

namespace App\Http\Services\USSD\SwascoUpdateMobile;

use App\Http\DTOs\BaseDTO;

class SwascoUpdateMobile_Step_4
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) > 3) {
         $txDTO->stepProcessed = true;
         $txDTO->error = "Duplicated request from ".$txDTO->mnoName.
                                 " with input: ".$txDTO->subscriberInput; 
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;

   }

}