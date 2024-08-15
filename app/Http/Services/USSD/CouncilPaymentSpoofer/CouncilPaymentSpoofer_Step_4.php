<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\USSD\StepServices\GetSpoofedMenu;
use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentSpoofer_Step_4 
{

   public function __construct(
      private BillingCredentialService $billingCredentials,
      private GetSpoofedMenu $getSpoofedMenu,
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $billingCredential = $this->billingCredentials->findOneBy(['client_id' =>$txDTO->client_id,
                                                                     'key' =>$txDTO->subscriberInput]);
         $clientMenu = $this->getSpoofedMenu->handle($txDTO);
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
         $txDTO->error = 'Council proxy payment step 4. '. $e->getMessage();
         return $txDTO;
      }
      return $txDTO;
      
   }
   
}