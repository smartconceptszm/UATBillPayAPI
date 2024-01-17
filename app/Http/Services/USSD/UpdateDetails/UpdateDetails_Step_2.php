<?php

namespace App\Http\Services\USSD\UpdateDetails;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails_Step_2
{

   public function __construct(
      private GetCustomerAccount $getCustomerAccount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         $txDTO->customer = $this->getCustomerAccount->handle($txDTO);
         $txDTO->district = $txDTO->customer['district'];
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
      return $txDTO;

   }

}