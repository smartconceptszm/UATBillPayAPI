<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class Payments_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
         $txDTO->stepProcessed = true;
         try {    
            $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
            if($momoPaymentStatus['enabled']){
               $prePaidText = $txDTO->menu === "BuyUnits" ? "PRE-PAID": "";
               $txDTO->response = $this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
            }else{
               $txDTO->response = $momoPaymentStatus['responseText'];
               $txDTO->lastResponse = true;
            }
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'MoMoOffline';
            }else{
               $txDTO->error = 'Pay bill sub step 1. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
            }
         }
      }
      return $txDTO;
      
   }
   
}