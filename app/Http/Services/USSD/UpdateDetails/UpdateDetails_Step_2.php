<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\Services\Web\MenuConfigs\CustomerFieldService;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_Step_2
{

   public function __construct(
      private CustomerFieldService $customerFieldService,
      private IEnquiryHandler $getCustomerAccount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->customerAccount = $txDTO->subscriberInput;
         $txDTO = $this->getCustomerAccount->handle($txDTO);
         $customerFields = $this->customerFieldService->findAll([
                                             'client_id' => $txDTO->client_id
                                          ]);
         if(count($customerFields)==1){
            $txDTO->customerJourney .= "*".$txDTO->subscriberInput;
            $txDTO->subscriberInput = "1";
            $customerField = $customerFields[0];
            $txDTO->response = "Update ".$customerField->prompt." on:\n". 
                              "Acc: ".$txDTO->subscriberInput."\n".
                              "Name: ".$txDTO->customer['name']."\n";
            if(array_key_exists($customerField->name,$txDTO->customer)){
               $txDTO->response .= "Current value: ".$txDTO->customer[$customerField->name]."\n\n";
            }
            $txDTO->response .= "Enter ".$customerField->prompt;
            if($customerField->placeHolder){
               $txDTO->response .= "(e.g. ".$customerField->prompt.")";
            }
            $txDTO->response .= "\n";
         }else{
               $txDTO->response = "Update details on:\n". 
               "Acc: ".$txDTO->subscriberInput."\n".
               "Name: ".$txDTO->customer['name']."\n". 
               "Addr: ".$txDTO->customer['address']."\n". 
               "Mobile: ".$txDTO->customer['mobileNumber']."\n\n".
               "Enter\n". 
                        "1. Confirm\n";                    
         }
         
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidAccount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = $e->getMessage(); 
      }
      return $txDTO;

   }

}