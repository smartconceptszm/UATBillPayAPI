<?php

namespace App\Http\Services\USSD\Utility;

use Exception;

class StepService_GetAmount 
{

   public function handle(string $subscriberInput, string $urlPrefix, string $mobileNumber):string
   {
      $subscriberInput = \str_replace("ZMW", "", $subscriberInput);
      $subscriberInput = \str_replace("ZMK", "", $subscriberInput);
      $subscriberInput = \str_replace(" ", "", $subscriberInput);
      $subscriberInput = \str_replace("K", "",$subscriberInput);
      $subscriberInput = \str_replace(",", "",$subscriberInput);
      $subscriberInput = number_format((float)$subscriberInput, 2, '.', ',');
      $minPaymentAmount = (float)\env(\strtoupper($urlPrefix).'_MIN_PAYMENT_AMOUNT');
      $maxPaymentAmount = (float)\env(\strtoupper($urlPrefix).'_MAX_PAYMENT_AMOUNT');
      $amount = (float)\str_replace(",", "",$subscriberInput);
      $testMSISDN = \explode("*", 
                           \env('APP_TEST_MSISDN')."*".
                           \env(\strtoupper($urlPrefix).'_APP_TEST_MSISDN'));
      if ((($amount< $minPaymentAmount) || $amount > $maxPaymentAmount)
               && !(\in_array($mobileNumber, $testMSISDN))) {
         throw new Exception("InvalidAmount", 1);
      }
      return $subscriberInput;
   }
    
}
