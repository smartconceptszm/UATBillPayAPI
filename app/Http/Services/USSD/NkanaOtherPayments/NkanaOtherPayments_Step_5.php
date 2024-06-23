<?php

namespace App\Http\Services\USSD\NkanaOtherPayments;

use App\Http\Services\USSD\Utility\StepService_ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class NkanaOtherPayments_Step_5
{

   public function __construct(
      private StepService_ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {
      return $this->confirmToPay->handle($txDTO);
   }
    
}