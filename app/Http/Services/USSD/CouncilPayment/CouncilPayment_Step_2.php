<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Clients\ClientRevenueCodeService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_2 
{

   public function __construct(
      private ClientRevenueCodeService $clientRevenueCodeService,
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $revenueCode = $this->clientRevenueCodeService->findOneBy(['menu_id' =>$txDTO->menu_id,
                                                                     'code' =>$txDTO->subscriberInput]);
         if($revenueCode){
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
            $txDTO->errorType = 'InvalidAccount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council payment step 2. '. $e->getMessage();
         return $txDTO;
      }
      return $txDTO;
      
   }
   
}