<?php

namespace App\Http\Services\USSD\OtherPayments;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class OtherPayment_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private OtherPaymentTypeService $otherPayTypes)
   {}

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
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'MoMoOffline';
            }else{
               $txDTO->error = 'Pay for other services step 1. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
            }
         }
      }
      return $txDTO;
      
   }
}