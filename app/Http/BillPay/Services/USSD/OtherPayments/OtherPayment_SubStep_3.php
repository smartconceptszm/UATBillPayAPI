<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class OtherPayment_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $otherPayTypes;
   public function __construct(OtherPaymentTypeService $otherPayTypes)
   {
      $this->otherPayTypes = $otherPayTypes;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
         $txDTO->stepProcessed = true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber =  $txDTO->subscriberInput;
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $paymentType = $this->otherPayTypes->findOneBy([
                        'client_id' => $txDTO->client_id,
                        'order' => $arrCustomerJourney[2]
                  ]);
            if($paymentType->hasReference == 'YES'){
               $txDTO->response = $paymentType->prompt."\n";
            }else{
               $txDTO->customerJourney = $txDTO->customerJourney.'*'.$txDTO->subscriberInput;
               $txDTO->subscriberInput = ' - ';
               $txDTO->response = "Enter Amount :\n";
            }
         } catch (\Throwable $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error='At other payments step 3. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}