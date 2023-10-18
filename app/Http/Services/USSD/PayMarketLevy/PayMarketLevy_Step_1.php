<?php

namespace App\Http\Services\USSD\PayMarketLevy;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayMarketLevy_Step_1
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($momoPaymentStatus['enabled']){
            $txDTO->response = "Enter the Market name and stand number:\n";
         }else{
            $txDTO->response = $momoPaymentStatus['responseText'];
            $txDTO->lastResponse= true;
         }
      } catch (Exception $e) {
         $txDTO->error = 'Pay market levy step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
   
}