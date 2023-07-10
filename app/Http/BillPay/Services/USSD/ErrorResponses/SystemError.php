<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\BillPay\DTOs\BaseDTO;

class SystemError implements IErrorResponse
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        try {    
            $txDTO->response = \env('ERROR_MESSAGE');
            $txDTO->lastResponse= true;
        } catch (\Throwable $e) {
            $txDTO->error = 'At Generate system error response. '.$e->getMessage();
        }
        return $txDTO;
        
    }
}