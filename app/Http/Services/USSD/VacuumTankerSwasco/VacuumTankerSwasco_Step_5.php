<?php

namespace App\Http\Services\USSD\VacuumTankerSwasco;

use App\Http\DTOs\BaseDTO;
use Exception;

class VacuumTankerSwasco_Step_5
{

   public function run(BaseDTO $txDTO)
   {

      try {
         if ($txDTO->subscriberInput == '1') {
            $txDTO->response = \strtoupper($txDTO->urlPrefix)." Payment request submitted to ".$txDTO->mnoName."\n".
                                 "You will receive a PIN prompt shortly!"."\n\n";
            $txDTO->fireMoMoRequest= true;
            $txDTO->status = 'COMPLETED';
            $txDTO->lastResponse = true;
         }else{
            if (\strlen($txDTO->subscriberInput) > 1) {
                  throw new Exception("Customer most likely put in PIN instead of '1' to confirm", 1);
            }
            throw new Exception("Invalid confirmation", 1);
         }
      } catch (Exception $e) {
         $txDTO->errorType = 'InvalidConfirmation';
         $txDTO->error = $e->getMessage();
      }
      return $txDTO;
      
   }

}