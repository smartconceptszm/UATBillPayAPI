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
            throw new Exception($momoPaymentStatus['responseText'], 1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'MoMoNotActivated';
         }else{
            $txDTO->error = 'Pay market levy step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
   
}