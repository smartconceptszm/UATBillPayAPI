<?php

namespace App\Http\Services\USSD\ReconnectionFeesSwasco;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\USSD\Utility\StepService_GetAmount;
use App\Http\DTOs\BaseDTO;
use Exception;

class ReconnectionFeesSwasco_Step_4
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount,
      private StepService_GetAmount $getAmount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try{
         try {
            [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
         } catch (Exception $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAmount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }
         try {
            [$txDTO->customer, $txDTO->district] = $this->getCustomerAccount->handle($txDTO);
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }
         $txDTO->response = "Pay ZMW " . $txDTO->subscriberInput . "\n" .
                                          "Into: Customer account - " . $txDTO->accountNumber;
         $txDTO->response .= " ".$txDTO->customer['name']. "\n";
         $txDTO->response = $txDTO->response."For: Reconnection fee\n";
         $txDTO->response .= "\nEnter\n" .
                              "1. Confirm\n" .
                              "0. Back";    
      } catch (Exception $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error = "At pay reconnection fees step 4: ".$e->getMessage();
      }
      return $txDTO;
      
   }
}