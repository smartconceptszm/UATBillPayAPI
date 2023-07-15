<?php

namespace App\Http\BillPay\Services\USSD\Payments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\DTOs\BaseDTO;

class Payments_SubStep_2 extends EfectivoPipelineWithBreakContract
{

    protected function stepProcess(BaseDTO $txDTO)
    {

      if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
         $txDTO->stepProcessed=true;
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->response="Enter Amount :\n";
      }
      return $txDTO;
        
    }
}