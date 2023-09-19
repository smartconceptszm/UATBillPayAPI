<?php

namespace App\Http\Services\USSD\OtherPayments;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class OtherPayment_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private OtherPaymentTypeService $otherPayTypes)
   {}

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
         } catch (Exception $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error='At other payments step 3. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}