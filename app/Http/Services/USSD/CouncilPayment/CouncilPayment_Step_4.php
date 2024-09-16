<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Gateway\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Web\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_4
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts,
      private BillingCredentialService $billingCredentials,
      private ClientMenuService $clientMenuService,
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
         $txDTO->error = 'Council payment step 4. '. $e->getMessage();
         return $txDTO;
      }

      $txDTO->response = "Pay ZMW ".$txDTO->subscriberInput."\n";
      $customerJourney = \explode("*", $txDTO->customerJourney);
      $billingCredential = $this->billingCredentials->findOneBy(['client_id' =>$txDTO->client_id,
                                                                     'key' =>$customerJourney[2]]);
      $txDTO->response .= "For: (".$billingCredential->key.") - ".$billingCredential->keyValue."\n";
      $txDTO->response .= "Name: ".$txDTO->reference."\n";
      $txDTO->response .= "Enter\n". 
                           "1. Confirm\n".
                           "2. Use different wallet\n".
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