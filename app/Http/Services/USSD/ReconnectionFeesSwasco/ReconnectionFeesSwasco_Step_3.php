<?php

namespace App\Http\Services\USSD\ReconnectionFeesSwasco;

use App\Http\DTOs\BaseDTO;
use Exception;

class ReconnectionFeesSwasco_Step_3
{

   public function run(BaseDTO $txDTO)
   {
      
      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber =  $txDTO->subscriberInput;
         $txDTO->response = "Enter Amount :\n";
      } catch (Exception $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error='At pay reconnection fees step 3. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}