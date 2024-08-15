<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class CouncilPayment_Step_5
{

   public function __construct(
      private ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {
      $txDTO = $this->confirmToPay->handle($txDTO);
      if($txDTO->error !=''){
         $txDTO->error = 'Council payment step 5. '.$txDTO->error;
      }
      return $txDTO;
   }
    
}