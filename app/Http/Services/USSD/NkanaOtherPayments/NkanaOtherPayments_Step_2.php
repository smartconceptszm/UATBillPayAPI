<?php

namespace App\Http\Services\USSD\NkanaOtherPayments;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\DTOs\BaseDTO;

class NkanaOtherPayments_Step_2
{

   public function __construct(
      private BillingCredentialService $billingCredentialService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $clientCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
         $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
         $txDTO->subscriberInput = $clientCredentials[$txDTO->menuPrompt];
         $txDTO->accountNumber = $clientCredentials[$txDTO->menuPrompt];
         $txDTO->customer['accountNumber'] = $clientCredentials[$txDTO->menuPrompt];
         $txDTO->customer['name'] = $txDTO->menuPrompt;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = "InvalidInput";
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At pay for other Nkana services step 2. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}