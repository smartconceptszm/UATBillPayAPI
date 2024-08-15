<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\USSD\StepServices\GetSpoofedMenu;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentSpoofer_Step_3 
{

   public function __construct(
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ValidateCRMInput $validateCRMInput,
      private GetSpoofedMenu $getSpoofedMenu,
      private ClientMenuService $clientMenuService,
      private MnoService $mnoService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    

         $txDTO->subscriberInput = $this->validateCRMInput->handle('MOBILE',$txDTO->subscriberInput);
         $mnoName = MNOs::getMNO(substr($txDTO->subscriberInput,0,5));
         $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);  

         $txDTO->payments_provider_id = $mno->payments_provider_id;
         $paymentProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if($paymentProviderStatus['enabled']){
            $clientMenu = $this->getSpoofedMenu->handle($txDTO);
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
            throw new Exception($paymentProviderStatus['responseText'], 2);
         }
      } catch (\Throwable $e) {
         switch ($e->getCode()) {
            case 1:
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'InvalidInput';
               break;
            case 2:
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'WalletNotActivated';
               break;
            default:
               $txDTO->error = 'Council payment step 3. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
               break;
         }
         
      }
      return $txDTO;
      
   }
   
}