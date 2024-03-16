<?php

namespace App\Http\Services\USSD\Utility;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class StepService_GetAmount 
{

   public function handle(BaseDTO $txDTO):array
   {
      $subscriberInput = \str_replace("ZMW", "", $txDTO->subscriberInput);
      $subscriberInput = \str_replace("ZMK", "", $subscriberInput);
      $subscriberInput = \str_replace(" ", "", $subscriberInput);
      $subscriberInput = \str_replace("K", "",$subscriberInput);
      $subscriberInput = \str_replace(",", "",$subscriberInput);
      $subscriberInput = number_format((float)$subscriberInput, 2, '.', ',');
      $minPaymentAmount = (float)\env(\strtoupper($txDTO->urlPrefix).'_MIN_PAYMENT_AMOUNT');
      $maxPaymentAmount = (float)\env(\strtoupper($txDTO->urlPrefix).'_MAX_PAYMENT_AMOUNT');
      $amount = (float)\str_replace(",", "",$subscriberInput);

      $testMSISDN = \explode("*", \env('APP_ADMIN_MSISDN')."*".$txDTO->testMSISDN);

      if(\env(\strtoupper($txDTO->urlPrefix).'_PREPAID_DEBT_BUFFER')){
         $minPaymentAmount = number_format((float)$txDTO->customer['balance'], 2, '.', ',');
         if ($amount< $minPaymentAmount) {
            Cache::put($txDTO->urlPrefix.$txDTO->accountNumber."_MinPaymentAmount", $minPaymentAmount, 
                              Carbon::now()->addMinutes(intval(\env('CUSTOMER_ACCOUNT_CACHE'))));
            throw new Exception("InvalidAmount", 1);
         }
      }else{
         if ((($amount< $minPaymentAmount) || $amount > $maxPaymentAmount)
                  && !(\in_array($txDTO->mobileNumber, $testMSISDN))) {
            Cache::put($txDTO->urlPrefix.$txDTO->accountNumber."_MinPaymentAmount", $minPaymentAmount, 
                  Carbon::now()->addMinutes(intval(\env('CUSTOMER_ACCOUNT_CACHE'))));
            throw new Exception("InvalidAmount", 1);
         }
      }


      return [$subscriberInput, $amount];
   }
    
}
