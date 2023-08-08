<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class OtherPayment_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $checkPaymentsEnabled;
   private $otherPayTypes;
   public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      OtherPaymentTypeService $otherPayTypes)
   {
      $this->checkPaymentsEnabled = $checkPaymentsEnabled;
      $this->otherPayTypes = $otherPayTypes;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
         $txDTO->stepProcessed=true;
         try {    
            $momoPaymentStatus =  $this->checkPaymentsEnabled->handle($txDTO);
            if($momoPaymentStatus['enabled']){
               $otherPayTypes = $this->otherPayTypes->findAll(['client_id' => $txDTO->client_id]);
               $stringMenu = "Select:\n";
               foreach ($otherPayTypes as $otherPayType) {
                  $stringMenu .= $otherPayType->order.'. '.$otherPayType->name."\n";
               }
               $txDTO->response = $stringMenu; 
            }else{
               $txDTO->response = $momoPaymentStatus['responseText'];
               $txDTO->lastResponse= true;
            }
         } catch (\Throwable $e) {
            $txDTO->error = 'Payfor other services step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
}