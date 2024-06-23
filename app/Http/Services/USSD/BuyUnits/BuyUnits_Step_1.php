<?php

namespace App\Http\Services\USSD\BuyUnits;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class BuyUnits_Step_1
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $paymentsProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($paymentsProviderStatus['enabled']){
            $txDTO->response = $this->accountNoMenu->handle($txDTO);
         }else{
            throw new Exception($paymentsProviderStatus['responseText'], 1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'PaymentProviderNotActivated';
         }else{
            $txDTO->error = 'Buy units sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
   
}