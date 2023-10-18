<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;

class ServiceApplications_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
         $txDTO->error = "Duplicated request from ".$txDTO->mnoName.
                                 " with input: ".$txDTO->subscriberInput; 
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;

   }

}