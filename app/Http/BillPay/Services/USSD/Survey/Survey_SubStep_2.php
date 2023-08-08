<?php

namespace App\Http\BillPay\Services\USSD\Survey;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;
use Exception;

class Survey_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   private $getCustomerAccount;
   public function __construct(StepService_GetCustomerAccount $getCustomerAccount)
   {
      $this->getCustomerAccount = $getCustomerAccount;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 2){
         $txDTO->stepProcessed = true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            $txDTO->customer = $this->getCustomerAccount->handle(
                                    $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
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
         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage(); 
         }
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