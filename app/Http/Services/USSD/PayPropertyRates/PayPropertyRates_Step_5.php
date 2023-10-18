<?php

namespace App\Http\Services\USSD\PayPropertyRates;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayPropertyRates_Step_5 
{

   public function run(BaseDTO $txDTO)
   {
      if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
         try {
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
         } catch (Exception $e) {
            $txDTO->error = 'Property rates step 5. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;        
   }

}