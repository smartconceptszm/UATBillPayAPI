<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use Illuminate\Support\Facades\Cache;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;
use Exception;

class UpdateDetails_SubStep_2 extends EfectivoPipelineWithBreakContract
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

}