<?php

namespace App\Http\Services\USSD\MakePayment;

use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class MakePayment_Step_5
{

   public function __construct(
      private ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {
      $txDTO = $this->confirmToPay->handle($txDTO);
      if($txDTO->error !=''){
         $txDTO->error = 'Make payment step 5. '.$txDTO->error;
      }
      return $txDTO;
   }
    
}