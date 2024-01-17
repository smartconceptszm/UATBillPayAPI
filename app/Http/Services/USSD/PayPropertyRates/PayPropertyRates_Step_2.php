<?php

namespace App\Http\Services\USSD\PayPropertyRates;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayPropertyRates_Step_2
{

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         $txDTO->error = 'Pay property rates sub step 2. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}