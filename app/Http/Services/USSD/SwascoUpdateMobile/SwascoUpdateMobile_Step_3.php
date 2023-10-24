<?php

namespace App\Http\Services\USSD\SwascoUpdateMobile;

use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\DTOs\BaseDTO;
use Exception;

class SwascoUpdateMobile_Step_3
{

   public function __construct(
      private StepService_ValidateCRMInput $validateCRMInput,
      private IBillingClient $billingClient)
   {} 

   public function run(BaseDTO $txDTO)
   {
      
      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->subscriberInput = $this->validateCRMInput->handle('MOBILE',$txDTO->subscriberInput);
         $caseNumber = $this->billingClient->changeCustomerDetail([
                                    'accountNumber' => $txDTO->accountNumber,
                                    "phoneNumber" => $txDTO->mobileNumber,
                                    'newMobileNo' => $txDTO->subscriberInput
                                 ]);
         $txDTO->response = "Application to change customer mobile number successfully submitted. Case number: ".
                              $caseNumber; 
         $txDTO->lastResponse = true;
         $txDTO->status='COMPLETED';

      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At update details step 3. '.$e->getMessage();
      }
      return $txDTO;

   }

}