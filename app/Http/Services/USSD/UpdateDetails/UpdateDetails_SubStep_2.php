<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_GetCustomerAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_GetCustomerAccount $getCustomerAccount)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 2){
         $txDTO->stepProcessed = true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            $txDTO->customer = $this->getCustomerAccount->handle(
                                    $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
            $txDTO->response = "Update details on:\n". 
            "Acc: ".$txDTO->subscriberInput."\n".
            "Name: ".$txDTO->customer['name']."\n". 
            "Addr: ".$txDTO->customer['address']."\n". 
            "Mobile: ".$txDTO->customer['mobileNumber']."\n\n".
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
            $txDTO->error = $e->getMessage(); 
         }
      }
      return $txDTO;

   }

}