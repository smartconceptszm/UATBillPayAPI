<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_2 
{

   public function __construct(
      private BillingCredentialService $billingCredentials,
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $billingCredential = $this->billingCredentials->findOneBy(['client_id' =>$txDTO->client_id,
                                                                     'key' =>$txDTO->subscriberInput]);
         if($billingCredential){
            $txDTO->customerAccount = $txDTO->subscriberInput;
            if($clientMenu->requiresReference == 'YES'){
               $txDTO->response = "Enter ".$clientMenu->referencePrompt.":\n";
            }else{
               $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
               $txDTO->subscriberInput = $txDTO->mobileNumber;
               $txDTO->response="Enter Amount :\n";
            }
         }else{
            throw new Exception("Invalid ".$clientMenu->customerAccountPrompt, 1);
         }

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = 'InvalidAmount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council payment step 2. '. $e->getMessage();
         return $txDTO;
      }
      return $txDTO;
      
   }
   
}