<?php

namespace App\Http\Services\USSD\OtherPayments;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\Services\USSD\Utility\StepService_GetAmount;
use App\Http\DTOs\BaseDTO;
use Exception;

class OtherPayment_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private OtherPaymentTypeService $otherPayTypes,
      private StepService_GetCustomerAccount $getCustomerAccount,
      private StepService_GetAmount $getAmount)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
         $txDTO->stepProcessed = true;
         try{
            $customerJourney = \explode("*", $txDTO->customerJourney);
            try {
               $txDTO->subscriberInput = $this->getAmount->handle($txDTO->subscriberInput,
                                          $txDTO->urlPrefix, $txDTO->mobileNumber);
            } catch (Exception $e) {
               if($e->getCode()==1){
                  $txDTO->errorType = 'InvalidAmount';
               }else{
                  $txDTO->errorType = 'SystemError';
               }
               $txDTO->error = $e->getMessage();
               return $txDTO;
            }
            $paymentType = $this->otherPayTypes->findOneBy([
                        'client_id' => $txDTO->client_id,
                        'order' => $customerJourney[2]
                  ]);
            if($paymentType->receiptAccount == 'CUSTOMER'){
               try {
                  $txDTO->customer  = $this->getCustomerAccount->handle($txDTO->accountNumber,
                                       $txDTO->urlPrefix, $txDTO->client_id);
               } catch (Exception $e) {
                  if($e->getCode() == 1){
                     $txDTO->errorType = 'InvalidAccount';
                  }else{
                     $txDTO->errorType = 'SystemError';
                  }
                  $txDTO->error = $e->getMessage();
                  return $txDTO;
               }
            }
            $txDTO->response = "Pay ZMW " . $txDTO->subscriberInput . "\n" .
            "Into: " . $paymentType->receiptAccount . " - " . $txDTO->accountNumber;
            if ($paymentType->receiptAccount == 'CUSTOMER') {
               $txDTO->response .= " ".$txDTO->customer['name']. "\n";
            }else{
               $txDTO->response .= "\n";
            }
            $txDTO->response = $txDTO->response."For: " . $paymentType->name . "\n";
            if ($paymentType->hasReference == 'YES') {
               $txDTO->response .= "Reference: " . $customerJourney[4] . "\n";
            }
            $txDTO->response .= "\nEnter\n" .
                                 "1. Confirm\n" .
                                 "0. Back";    
         } catch (Exception $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error = "At other payment step 5: ".$e->getMessage();
         }
      }
      return $txDTO;
      
   }
}