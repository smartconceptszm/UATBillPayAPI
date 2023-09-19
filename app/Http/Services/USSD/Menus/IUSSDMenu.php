<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\DTOs\BaseDTO;

interface IUSSDMenu 
{
   public function handle(BaseDTO $txDTO): BaseDTO;
}