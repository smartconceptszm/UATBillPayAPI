<?php

namespace App\Http\Services\USSD\Survey;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

use Exception;

class Survey_Step_2
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->customer = $this->getCustomerAccount->handle($txDTO->accountNumber,$txDTO->urlPrefix);
         $txDTO->response = "Good ".$this->timeofDay().",\n". 
            $txDTO->customer['name']." (".$txDTO->subscriberInput.")\n". 
            "Thank you for participating in this survey.\n". 
            "All data will be used in confidence\n\n".
            "Enter\n". 
                     "1. Confirm\n".
                     "0. Back";
         $cacheValue = \json_encode([
                              'must'=>false,
                              'steps'=>1,
                        ]);                    
         Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
               Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
      } catch (Exception $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidAccount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Survey step 2. '.$e->getMessage();
      }
      return $txDTO;

   }

   private function timeofDay(): string
   {
      $myTime = Carbon::now();
      $hour = $myTime->format('H');
      if ($hour < 12) {
         return 'morning';
      }
      if ($hour < 17) {
         return 'afternoon';
      }
      return 'evening';
   }

}