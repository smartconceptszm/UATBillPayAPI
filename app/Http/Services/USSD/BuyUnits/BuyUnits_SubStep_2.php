<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class BuyUnits_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
         try {
            $txDTO->stepProcessed = true;
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            $txDTO->response = "Enter Amount :\n";
         } catch (Exception $e) {
            $txDTO->error = 'Buy units sub step 2. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }

      }
      return $txDTO;
      
   }

}