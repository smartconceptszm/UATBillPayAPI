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
            $txDTO->subscriberInput = $this->getAmount->handle($txDTO->subscriberInput,
                                       $txDTO->urlPrefix, $txDTO->mobileNumber);
         } catch (Exception $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAmount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }

         $txDTO->response = "Pay ZMW " . $txDTO->subscriberInput . "\n" .
                              "Into: GENERAL LEDGER - " . $txDTO->accountNumber."\n";
         $txDTO->response = $txDTO->response."For: Vaccoum tanker pit emptying\n";
         $txDTO->response .= "Reference: " . $customerJourney[2] . "\n";
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