<?php

namespace App\Http\Services\USSD\PayMarketLevy;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayMarketLevy_Step_2
{

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->reference = $txDTO->subscriberInput;
         $txDTO->accountNumber = $txDTO->mobileNumber;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         $txDTO->error = 'Pay market levy step 2. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}