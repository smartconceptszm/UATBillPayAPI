<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\ClientOtherPaymentTypeViewService;
use App\Http\BillPay\DTOs\BaseDTO;

class PayOther_SubStep_1 extends EfectivoPipelineWithBreakContract
{

   private $checkPaymentsEnabled;
   private $otherPayTypes;
   private $accountNoMenu;
   public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      ClientOtherPaymentTypeViewService $otherPayTypes, 
      StepService_AccountNoMenu $accountNoMenu)
   {
      $this->checkPaymentsEnabled = $checkPaymentsEnabled;
      $this->otherPayTypes = $otherPayTypes;
      $this->accountNoMenu = $accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
         $txDTO->stepProcessed=true;
         try {    
            $momoPaymentStatus =  $this->checkPaymentsEnabled->handle($txDTO);
            if($momoPaymentStatus['enabled']){
               $otherPayTypes = $this->otherPayTypes->findAll(['client_id' => $txDTO->client_id]);
               if(\count($otherPayTypes) > 1){
                  $stringMenu = "Select:\n";
                  foreach ($otherPayTypes as $otherPayType) {
                     $stringMenu .= $otherPayType->order.'. '.$otherPayType->name."\n";
                  }
                  $txDTO->response = $stringMenu; 
               }else{
                  $paymentType = $otherPayTypes[0];
                  if($paymentType->receiptAccount == 'CUSTOMER'){
                        $txDTO->response = $this->accountNoMenu->handle('',$txDTO->urlPrefix);
                  }else{
                     $txDTO->customer['accountNumber'] = $paymentType->ledgerAccountNumber;
                     $txDTO->customer['name'] = $paymentType->name;
                     $txDTO->response = $paymentType->prompt."\n";
                  }
                  $txDTO->customerJourney = $txDTO->customerJourney.'*'.$txDTO->subscriberInput;
                  $txDTO->subscriberInput = $paymentType->order;
               }

            }else{
               $txDTO->response = $momoPaymentStatus['responseText'];
               $txDTO->lastResponse= true;
            }
         } catch (\Throwable $e) {
            $txDTO->error = 'Payments sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
}