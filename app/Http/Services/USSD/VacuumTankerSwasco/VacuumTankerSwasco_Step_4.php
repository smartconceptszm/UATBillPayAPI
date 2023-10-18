<?php

namespace App\Http\Services\USSD\VacuumTankerSwasco;

use App\Http\Services\USSD\Utility\StepService_GetAmount;
use App\Http\DTOs\BaseDTO;
use Exception;

class VacuumTankerSwasco_Step_4
{

   public function __construct(
      private StepService_GetAmount $getAmount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try{
         $customerJourney = \explode("*", $txDTO->customerJourney);
         try {
            [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
         } catch (Exception $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAmount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }

         $txDTO->response = "Pay ZMW " . $txDTO->subscriberInput . " to SWASCO\n" .
                              "For: Vaccoum tanker pit emptying\n";
         $txDTO->response .= "Reference: " . $customerJourney[3] . "\n";
         $txDTO->response .= "\nEnter\n" .
                              "1. Confirm\n" .
                              "0. Back";    
      } catch (Exception $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error = "At pay for vacuum tanker step 4: ".$e->getMessage();
      }
      return $txDTO;
      
   }
}