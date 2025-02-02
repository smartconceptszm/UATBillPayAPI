<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class FaultsComplaints_Step_6
{

   public function run(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) > 5) {
         $txDTO->error = "Duplicated request from ".$txDTO->mnoName.
                                 " with input: ".$txDTO->subscriberInput; 
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;

   }

}