<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\ClientOtherPaymentTypeViewService;
use App\Http\BillPay\DTOs\BaseDTO;

class PayOther_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $otherPayTypes;
   public function __construct(ClientOtherPaymentTypeViewService $otherPayTypes)
   {
      $this->otherPayTypes = $otherPayTypes;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
         try {
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $paymentType = $this->otherPayTypes->findOneBy([
                        'client_id' => $txDTO->client_id,
                        'order' => $arrCustomerJourney[2]
                  ]);
            if($paymentType->receiptAccount == 'CUSTOMER'){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               $txDTO->accountNumber = $txDTO->subscriberInput;
            }

            if ( $paymentType->hasApplicationNo == 'YES') {
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               $txDTO->accountNumber = $txDTO->subscriberInput;
            }
            $txDTO->response = "Enter amount:\n";
         } catch (\Throwable $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error='At capturing payment narration. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}