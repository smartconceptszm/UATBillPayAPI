<?php

namespace App\Http\Services\USSD\GetLastToken;

use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetLastToken_Step_2
{

   public function __construct(
      private IEnquiryHandler $getCustomerAccount,
      private PaymentHistoryService $paymentHistoryService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->meterNumber = $txDTO->subscriberInput;
         $txDTO = $this->getCustomerAccount->handle($txDTO);
      } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error=$e->getMessage();
            return $txDTO;
      }

      
      try {
         
         $payment = $this->paymentHistoryService->getLatestToken(
                                                         [
                                                            'meterNumber' => $txDTO->meterNumber,
                                                            'client_id' => $txDTO->client_id
                                                         ]
                                                      );
         if($payment){
            $payment = (object)$payment->toArray();
            $txDTO->response = $payment->receipt;
         }else{
            $txDTO->response = "NO Token found for Meter Number: ".$txDTO->meterNumber;
         }
			$txDTO->lastResponse = true;
      } catch (\Throwable $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error = $e->getMessage();
         return $txDTO;
      }
      
      return $txDTO;
      
   }

}