<?php

namespace App\Http\BillPay\Services\USSD\CheckBalance;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\DTOs\BaseDTO;

class CheckBalance_SubStep_5 extends EfectivoPipelineWithBreakContract
{

    protected function stepProcess(BaseDTO $txDTO)
    {

        if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
            $txDTO->stepProcessed=true;
            $txDTO->error="Duplicated request from ".$txDTO->mnoName.
                                    " with input: ".$txDTO->subscriberInput; 
            $txDTO->errorType = 'SystemError';
        }
        return $txDTO;
        
    }
}