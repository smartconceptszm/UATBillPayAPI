<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\DTOs\BaseDTO;

interface IErrorResponse 
{
   public function handle(BaseDTO $txDTO): BaseDTO;
}