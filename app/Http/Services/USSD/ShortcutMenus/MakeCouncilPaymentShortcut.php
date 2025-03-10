<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class MakeCouncilPaymentShortcut
{

   public function __construct(
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ClientMenuService $clientMenuService)
   {}
   
   public function handle(BaseDTO $txDTO, $selectedMenu)
   {

      $txDTO->customer['customerAccount'] = $selectedMenu->commonAccount;
      $txDTO->customerAccount = $selectedMenu->commonAccount;
      $txDTO->customer['name'] = $selectedMenu->description;
      $txDTO->billingClient = $selectedMenu->billingClient; 
      $txDTO->menuPrompt = $selectedMenu->prompt;
      $txDTO->handler = $selectedMenu->handler; 
      $txDTO->menu_id = $selectedMenu->id; 

      $arrInputs = explode("*", $txDTO->subscriberInput);

      if(count($arrInputs) != 3){
         $txDTO->subscriberInput = $arrInputs[0];
         return $txDTO;
      }

      $txDTO->subscriberInput = $arrInputs[2];
      $txDTO->customerJourney = $arrInputs[0].'*'.$arrInputs[1];

      $walletStatus = $this->checkPaymentsEnabled->handle($txDTO);
      if(!$walletStatus['enabled']){
         throw new Exception($walletStatus['responseText'], 2);
      }

      return $txDTO;
       
   }

}