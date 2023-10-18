<?php

namespace App\Http\Services\USSD\PayBusLevy;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBusLevy_Step_1
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($momoPaymentStatus['enabled']){
            $txDTO->response = "Enter the Vehicle registration number and fleet number:\n";
         }else{
            $txDTO->response = $momoPaymentStatus['responseText'];
            $txDTO->lastResponse= true;
         }
      } catch (Exception $e) {
         $txDTO->error = 'Pay Bus levy step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
   
}