<?php

namespace App\Http\BillPay\Services\USSD\Utility;

use App\Http\BillPay\DTOs\BaseDTO;

class StepService_CheckPaymentsEnabled 
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        if (\env('APP_ENV') != 'Production'){
            $testMSISDN= \explode("*", 
                                \env('APP_TEST_MSISDN')."*".
                                \env(\strtoupper($txDTO->urlPrefix).'_APP_TEST_MSISDN'));
            if (!\in_array($txDTO->mobileNumber, $testMSISDN)){
                $txDTO->response = "Payment for ".\strtoupper($txDTO->urlPrefix).
                        " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                        "Thank you for your patience.";
                $txDTO->lastResponse=true;
            }
        }

        if (\env('APP_ENV') == 'Production' && 
                \env(\strtoupper($txDTO->urlPrefix).'_'.$txDTO->mnoName.'_ACTIVE')!='YES'){
            $txDTO->response = "Payment for ".\strtoupper($txDTO->urlPrefix).
                    " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                    "Thank you for your patience.";
            $txDTO->lastResponse=true;
        }
        return $txDTO;
        
    }
    
}
