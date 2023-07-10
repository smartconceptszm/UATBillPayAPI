<?php

namespace App\Http\BillPay\Services\USSD\ErrorResponses;

use App\Http\BillPay\DTOs\BaseDTO;

interface IErrorResponse 
{
   public function handle(BaseDTO $txDTO): BaseDTO;
}