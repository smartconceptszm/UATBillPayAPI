<?php

namespace App\Http\BillPay\Services\USSD\Utility;

use App\Http\BillPay\DTOs\BaseDTO;

class StepService_CheckPaymentsEnabled 
{

   public function handle(BaseDTO $txDTO):array
   {

      $response = [
                  'enabled'=>true,
                  'responseText' => ""
               ];
      if (\env('APP_ENV') != 'Production'){
         $testMSISDN= \explode("*", 
                              \env('APP_TEST_MSISDN')."*".
                              \env(\strtoupper($txDTO->urlPrefix).'_APP_TEST_MSISDN'));
         if (!\in_array($txDTO->mobileNumber, $testMSISDN)){
            $response['responseText'] = "Payment for ".\strtoupper($txDTO->urlPrefix).
                  " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
            $response['enabled'] = false;
         }
      }

      if (\env('APP_ENV') == 'Production' && 
               \env(\strtoupper($txDTO->urlPrefix).'_'.$txDTO->mnoName.'_ACTIVE')!='YES'){
         $response['responseText'] = "Payment for ".\strtoupper($txDTO->urlPrefix).
                  " Water bills via " . $txDTO->mnoName . " Mobile Money will be launched soon!" . "\n" .
                  "Thank you for your patience.";
         $response['enabled'] = false;
      }
      return $response;
      
   }
    
}
