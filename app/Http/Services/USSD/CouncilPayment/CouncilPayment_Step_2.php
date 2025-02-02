<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_2 
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->customerAccount = $txDTO->subscriberInput;
         if($clientMenu->requiresReference == 'YES'){
            $txDTO->response = "Enter ".$clientMenu->referencePrompt.":\n";
         }else{
            $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
            $txDTO->subscriberInput = $txDTO->mobileNumber;
            $txDTO->response="Enter Amount :\n";
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'Council payment step 2. '. $e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }
   
}