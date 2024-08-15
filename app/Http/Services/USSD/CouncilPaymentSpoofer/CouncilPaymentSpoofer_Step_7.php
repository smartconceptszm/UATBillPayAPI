<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\Services\USSD\StepServices\GetSpoofedMenu;
use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\Services\Web\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\BaseDTO;

class CouncilPaymentSpoofer_Step_7
{

   public function __construct(
      private GetSpoofedMenu $getSpoofedMenu,
      private ConfirmToPay $confirmToPay,
      private MnoService $mnoService) 
   {}

   public function run(BaseDTO $txDTO)
   {

      $customerJourney = \explode("*", $txDTO->customerJourney);
      $mnoName = MNOs::getMNO(substr($customerJourney[3],0,5));
      $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);  
      $txDTO->payments_provider_id = $mno->payments_provider_id;
      $txDTO->mobileNumber = $customerJourney[3];
      $clientMenu = $this->getSpoofedMenu->handle($txDTO);
      $txDTO->menu_id = $clientMenu->id;
      unset($customerJourney[1]);
      unset($customerJourney[3]);
      $txDTO->customerJourney = \implode("*",$customerJourney);
      $txDTO = $this->confirmToPay->handle($txDTO);
      if($txDTO->error !=''){
         $txDTO->error = 'Council proxy payment step 7. '.$txDTO->error;
      }
      return $txDTO;
   }
    
}