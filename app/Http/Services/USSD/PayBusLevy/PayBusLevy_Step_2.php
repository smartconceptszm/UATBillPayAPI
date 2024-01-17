<?php

namespace App\Http\Services\USSD\PayBusLevy;

use App\Http\DTOs\BaseDTO;
use Exception;

class PayBusLevy_Step_2
{

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->reference = $txDTO->subscriberInput;
         $txDTO->accountNumber = $txDTO->mobileNumber;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         $txDTO->error = 'Pay Bus levy step 2. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}