<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class OtherPayment_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   private $otherPayTypes;
   private $accountNoMenu;
   public function __construct(OtherPaymentTypeService $otherPayTypes,
               StepService_AccountNoMenu $accountNoMenu)
   {
      $this->otherPayTypes = $otherPayTypes;
      $this->accountNoMenu = $accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
         try {
            $txDTO->stepProcessed=true;
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $paymentType = $this->otherPayTypes->findOneBy([
                                          'client_id' => $txDTO->client_id,
                                          'order' => $txDTO->subscriberInput
                                    ]);
            if($paymentType->id){
               if($paymentType->receiptAccount == 'CUSTOMER'){
                  $txDTO->response = $this->accountNoMenu->handle('',$txDTO->urlPrefix);
               }else{
                  $txDTO->accountNumber = $paymentType->ledgerAccountNumber;
                  $txDTO->customer['accountNumber'] = $paymentType->ledgerAccountNumber;
                  $txDTO->customer['name'] = $paymentType->name;
                  if($paymentType->hasReference == 'YES'){
                     $txDTO->customerJourney = $txDTO->customerJourney.'*'.$txDTO->subscriberInput;
                     $txDTO->subscriberInput = $paymentType->ledgerAccountNumber;
                     $txDTO->response = $paymentType->prompt."\n";
                  }else{
                     $txDTO->customerJourney = $txDTO->customerJourney.'*'.$txDTO->subscriberInput."*".
                                                   $paymentType->ledgerAccountNumber;
                     $txDTO->subscriberInput = ' - ';
                     $txDTO->response = "Enter Amount :\n";
                  }
               }
            }else{
                  throw new Exception("Payment type not found", 1);
            }
         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = "InvalidInput";
           }else{
               $txDTO->errorType = 'SystemError';
           }
           $txDTO->error='At pay for other services step 2. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}