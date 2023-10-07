<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\DTOs\BaseDTO;

interface IReceiptPayment 
{
   public function handle(BaseDTO $momoDTO):BaseDTO;
}