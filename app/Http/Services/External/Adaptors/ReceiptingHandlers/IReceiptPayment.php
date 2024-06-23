<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\DTOs\BaseDTO;

interface IReceiptPayment 
{
   public function handle(BaseDTO $paymentDTO):BaseDTO;
}