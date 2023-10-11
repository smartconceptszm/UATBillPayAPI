<?php

namespace App\Http\Services\USSD\Utility;

use App\Http\DTOs\BaseDTO;
use Exception;

class StepService_GetAmount 
{

   public function handle(BaseDTO $txDTO):string
   {
      $subscriberInput = \str_replace("ZMW", "", $txDTO->subscriberInput);
      $subscriberInput = \str_replace("ZMK", "", $txDTO->subscriberInput);
      $subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
      $subscriberInput = \str_replace("K", "",$txDTO->subscriberInput);
      $subscriberInput = \str_replace(",", "",$txDTO->subscriberInput);
      $subscriberInput = number_format((float)$txDTO->subscriberInput, 2, '.', ',');
      $minPaymentAmount = (float)\env(\strtoupper($txDTO->urlPrefix).'_MIN_PAYMENT_AMOUNT');
      $maxPaymentAmount = (float)\env(\strtoupper($txDTO->urlPrefix).'_MAX_PAYMENT_AMOUNT');
      $amount = (float)\str_replace(",", "",$txDTO->subscriberInput);

      $testMSISDN = \explode("*", \env('APP_ADMIN_MSISDN')."*".$txDTO->testMSISDN);

      if ((($amount< $minPaymentAmount) || $amount > $maxPaymentAmount)
               && !(\in_array($txDTO->mobileNumber, $testMSISDN))) {
         throw new Exception("InvalidAmount", 1);
      }
      return $subscriberInput;
   }
    
}
