<?php

namespace App\Http\BillPay\Services\USSD\Utility;

use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class StepService_ConfirmToPay
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        if ($txDTO->subscriberInput == '1') {
            $txDTO->response = \strtoupper($txDTO->urlPrefix)." Payment request submitted to ".$txDTO->mnoName."\n".
                                "You will receive a PIN prompt shortly!"."\n\n";
            $txDTO->fireMoMoRequest= true;
            $txDTO->lastResponse = true;
            return $txDTO;
        }

        if (\strlen($txDTO->subscriberInput) > 1) {
            throw new Exception("Customer most likely put in PIN instead of '1' to confirm", 1);
        }
        return $txDTO;
        
    }
}