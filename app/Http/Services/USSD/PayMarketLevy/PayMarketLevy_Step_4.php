<?php

namespace App\Http\Services\USSD\PayMarketLevy;

use App\Http\Services\USSD\Utility\StepService_ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class PayMarketLevy_Step_4
{

   public function __construct(
      private StepService_ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {
      $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
      $txDTO->reference = $arrCustomerJourney[2];
      return $this->confirmToPay->handle($txDTO);
   }
    
}