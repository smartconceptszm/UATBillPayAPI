<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\BillPay\DTOs\BaseDTO;

class MaintenanceMode implements IErrorResponse
{

    public function handle(BaseDTO $txDTO):BaseDTO
    {

        try {    
            $txDTO->response = \env('MODE_MESSAGE');
            $txDTO->error=$txDTO->response;
            $txDTO->lastResponse = true;
        } catch (\Throwable $e) {
            $txDTO->error = 'At Generate maintenance mode response. '.$e->getMessage();
        }
        return $txDTO;
        
    }
}