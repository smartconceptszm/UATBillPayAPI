<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class Payments_SubStep_4 extends EfectivoPipelineWithBreakContract
{

    protected function stepProcess(BaseDTO $txDTO)
    {

      if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
         $txDTO->stepProcessed=true;
         try {
            if ($txDTO->subscriberInput == '1') {
               $txDTO->response = \strtoupper($txDTO->urlPrefix)." Payment request submitted to ".$txDTO->mnoName."\n".
                                    "You will receive a PIN prompt shortly!"."\n\n";
               $txDTO->fireMoMoRequest = true;
               $txDTO->status = 'COMPLETED';
               $txDTO->lastResponse = true;
            }else{
               if (\strlen($txDTO->subscriberInput) > 1) {
                  throw new Exception("Customer most likely put in PIN instead of '1' to confirm", 1);
               }
               throw new Exception("Invalid confirmation", 1);
            }
         } catch (Exception $e) {
            $txDTO->subscriberInput = $txDTO->customerJourney;
            $txDTO->errorType = 'InvalidConfimation';
            $txDTO->error = $e->getMessage();
            $txDTO->customerJourney = '';
         }
      }
      return $txDTO;
        
    }
    
}