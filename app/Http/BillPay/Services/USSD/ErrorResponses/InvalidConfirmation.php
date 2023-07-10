<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\BillPay\DTOs\BaseDTO;

class InvalidConfirmation implements IErrorResponse
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        try {    
            $txDTO->response = "Invalid selection. Please enter\n". 
                                "1. to Confirm payment\n".
                                "0. Back";
        } catch (\Throwable $e) {
            $txDTO->error = 'At Generate invalid input response. '.$e->getMessage();
        }
        return $txDTO;
        
    }
}