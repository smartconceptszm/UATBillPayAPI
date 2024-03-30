<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\DTOs\BaseDTO;

interface IReceiptPayment 
{
   public function handle(BaseDTO $momoDTO):BaseDTO;
}