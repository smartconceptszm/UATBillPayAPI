<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\DTOs\BaseDTO;

interface IUSSDMenu 
{
   public function handle(BaseDTO $txDTO): BaseDTO;
}