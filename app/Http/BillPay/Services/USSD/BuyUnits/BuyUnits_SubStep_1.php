<?php

namespace App\Http\BillPay\Services\USSD\BuyUnits;

use App\Http\BillPay\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class BuyUnits_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $checkPaymentsEnabled;
   private $accountNoMenu;
   public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      StepService_AccountNoMenu $accountNoMenu)
   {
      $this->checkPaymentsEnabled=$checkPaymentsEnabled;
      $this->accountNoMenu=$accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
         $txDTO->stepProcessed=true;
         try {    
            $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
            if($momoPaymentStatus['enabled']){
               $txDTO->response = $this->accountNoMenu->handle("PRE-PAID",$txDTO->urlPrefix);
            }else{
               $txDTO->response = $momoPaymentStatus['responseText'];
               $txDTO->lastResponse= true;
            }
         } catch (\Throwable $e) {
            $txDTO->error = 'Buy units sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
   
}