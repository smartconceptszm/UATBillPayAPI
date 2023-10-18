<?php

namespace App\Http\Services\USSD\PayMarketLevy;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayMarketLevy_Step_5 
{

   public function run(BaseDTO $txDTO)
   {
      if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
         try {
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
         } catch (Exception $e) {
            $txDTO->error = 'Market levy step 5. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;        
   }

}