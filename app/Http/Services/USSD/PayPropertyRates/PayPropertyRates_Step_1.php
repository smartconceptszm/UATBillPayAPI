<?php

namespace App\Http\Services\USSD\PayPropertyRates;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayPropertyRates_Step_1
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
            $txDTO->response = $this->accountNoMenu->handle($txDTO->urlPrefix);
         }else{
            $txDTO->response = $momoPaymentStatus['responseText'];
            $txDTO->lastResponse= true;
         }
      } catch (Exception $e) {
         $txDTO->error = 'Pay property rates sub step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
   
}