<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\USSD\StepServices\GetAmount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentSpoofer_Step_6
{

   public function __construct(
      private BillingCredentialService $billingCredentials,
      private GetAmount $getAmount
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {
         [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = 'InvalidAmount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council payment step 6. '. $e->getMessage();
         return $txDTO;
      }

      $customerJourney = \explode("*", $txDTO->customerJourney);
      $billingCredential = $this->billingCredentials->findOneBy(['client_id' =>$txDTO->client_id,
                                                                     'key' =>$customerJourney[4]]);
      $txDTO->response =   "Pay ZMW ".$txDTO->subscriberInput."\n".
                           "Using: ".$customerJourney[3]."\n".
                           "For: (".$billingCredential->key.") - ".$billingCredential->keyValue."\n".
                           "Enter\n". 
                              "1. Confirm\n".
                              "0. Back";
                           
      $cacheValue = \json_encode([
               'must'=>false,
               'steps'=>2,
         ]);         

      Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
         Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));

      return $txDTO;
      
   }

}