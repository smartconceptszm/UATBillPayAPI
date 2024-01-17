<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\DTOs\BaseDTO;
use Exception;

class BuyUnits_Step_2
{

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         $txDTO->error = 'Buy units sub step 2. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}