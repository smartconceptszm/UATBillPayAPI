<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\DTOs\BaseDTO;

interface IReceiptPayment 
{
   public function handle(BaseDTO $paymentDTO):BaseDTO;
}