<?php

namespace App\Http\Services\USSD\SwascoUpdateMobile;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\DTOs\BaseDTO;
use Exception;

class SwascoUpdateMobile_Step_2
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->customer = $this->getCustomerAccount->handle($txDTO);
         $txDTO->response = "Update Mobile on:\n". 
         "Acc: ".$txDTO->subscriberInput."\n".
         "Name: ".$txDTO->customer['name']."\n". 
         "Addr: ".$txDTO->customer['address']."\n". 
         "Current Mobile: ".$txDTO->customer['mobileNumber']."\n\n".

         "Enter the new Mobile No. e.g (095xxxxxxx)";

      } catch (Exception $e) {
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