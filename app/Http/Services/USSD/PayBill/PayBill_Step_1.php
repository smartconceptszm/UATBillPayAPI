<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBill_Step_1 
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($momoPaymentStatus['enabled']){
            $txDTO->response = $this->accountNoMenu->handle($txDTO->urlPrefix,$txDTO->accountType);
         }else{
            throw new Exception($momoPaymentStatus['responseText'], 1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'MoMoNotActivated';
         }else{
            $txDTO->error = 'Pay bill sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
   
}