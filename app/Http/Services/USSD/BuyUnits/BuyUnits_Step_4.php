<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\Services\USSD\Utility\StepService_ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class BuyUnits_Step_4
{

   public function __construct(
      private StepService_ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {
      return $this->confirmToPay->handle($txDTO);
   }
    
}