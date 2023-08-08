<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class OtherPayment_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   private $validateCRMInput;
   private $otherPayTypes;
   public function __construct(OtherPaymentTypeService $otherPayTypes,
      StepService_ValidateCRMInput $validateCRMInput)
   {
      $this->validateCRMInput = $validateCRMInput;
      $this->otherPayTypes = $otherPayTypes;
   }

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
         } catch (\Throwable $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error='At other payments step 4. '.$e->getMessage();
         }
      }
      return $txDTO;
      
   }

}