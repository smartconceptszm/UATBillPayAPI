<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;

class FaultsComplaints_SubStep_6 extends EfectivoPipelineWithBreakContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) > 5) {
         $txDTO->stepProcessed = true;
         $txDTO->error = "Duplicated request from ".$txDTO->mnoName.
                                 " with input: ".$txDTO->subscriberInput; 
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;

   }

}