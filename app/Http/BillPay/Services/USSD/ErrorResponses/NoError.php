<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\BillPay\DTOs\BaseDTO;

class NoError implements IErrorResponse
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        $txDTO->error = "";
        return $txDTO;
        
    }
}