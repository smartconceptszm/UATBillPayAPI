<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class BuyUnits_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {
      if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
         try {
            $txDTO->stepProcessed=true;
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
         } catch (Exception $e) {
            $txDTO->error = 'Buy units sub step 5. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;        
   }

}