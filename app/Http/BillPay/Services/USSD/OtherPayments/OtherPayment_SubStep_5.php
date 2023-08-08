<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetAmount;

use App\Http\BillPay\DTOs\BaseDTO;

class OtherPayment_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   private $getCustomerAccount;
   private $otherPayTypes;
   private $getAmount;
   public function __construct(OtherPaymentTypeService $otherPayTypes,
      StepService_GetCustomerAccount $getCustomerAccount,
      StepService_GetAmount $getAmount)
   {
      $this->getCustomerAccount = $getCustomerAccount;
      $this->otherPayTypes = $otherPayTypes;
      $this->getAmount = $getAmount;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
         $txDTO->stepProcessed = true;
         try{
            $customerJourney = \explode("*", $txDTO->customerJourney);
            try {
               $txDTO->subscriberInput = $this->getAmount->handle($txDTO->subscriberInput,
                                          $txDTO->urlPrefix, $txDTO->mobileNumber);
            } catch (\Throwable $e) {
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
               } catch (\Throwable $e) {
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
         } catch (\Throwable $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error = "At other payment step 5: ".$e->getMessage();
         }
      }
      return $txDTO;
      
   }
}