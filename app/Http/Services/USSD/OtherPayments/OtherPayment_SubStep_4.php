<?php

namespace App\Http\Services\USSD\OtherPayments;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class OtherPayment_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private OtherPaymentTypeService $otherPayTypes,
      private StepService_ValidateCRMInput $validateCRMInput)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
         $txDTO->stepProcessed = true;
         try {
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $paymentType = $this->otherPayTypes->findOneBy([
                        'client_id' => $txDTO->client_id,
                        'order' => $arrCustomerJourney[2]
                  ]);
            $txDTO->subscriberInput = $this->validateCRMInput->handle($paymentType,$txDTO->subscriberInput);
            $txDTO->response = "Enter Amount :\n";
         } catch (Exception $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error='At other payments step 4. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}