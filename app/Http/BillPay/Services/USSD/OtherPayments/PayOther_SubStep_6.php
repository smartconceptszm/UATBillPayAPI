<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\DTOs\BaseDTO;

class PayOther_SubStep_6 extends EfectivoPipelineWithBreakContract
{

    protected function stepProcess(BaseDTO $txDTO)
    {
        if (\count(\explode("*", $txDTO->customerJourney)) > 5) {
            $txDTO->stepProcessed = true;
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
        }
        return $txDTO;        
    }

}