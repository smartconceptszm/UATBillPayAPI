<?php

namespace App\Http\Services\USSD\MakePayment;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class MakePayment_Step_1 
{

   public function __construct(
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    
         $paymentProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($paymentProviderStatus['enabled']){
            $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
            if($clientMenu->onOneAccount == 'NO'){
               $txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
            }else{
               $txDTO->customerAccount = $clientMenu->commonAccount;
               $txDTO->customer['customerAccount'] = $clientMenu->commonAccount;
               $txDTO->customer['name'] = $clientMenu->description;
               if($clientMenu->requiresReference == 'YES'){
                  $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
                  $txDTO->subscriberInput = $clientMenu->commonAccount;
                  $txDTO->response = "Enter ".$clientMenu->referencePrompt.":\n";
               }else{
                  $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput."*".$clientMenu->commonAccount;
                  $txDTO->subscriberInput = $txDTO->mobileNumber;
                  $txDTO->response="Enter Amount :\n";
               }
            }
         }else{
            throw new Exception($paymentProviderStatus['responseText'], 1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = USSDStatusEnum::WalletNotActivated->value;
         }else{
            $txDTO->error = 'Make payment step 1. '.$e->getMessage();
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
         }
      }
      return $txDTO;
      
   }
   
}