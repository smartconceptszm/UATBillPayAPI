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

      if(count($arrInputs) < 3){
         $txDTO->subscriberInput = $arrInputs[0];
         return $txDTO;
      }

      $txDTO->subscriberInput = array_pop($arrInputs);
      if(count($arrInputs) == 3){
         $txDTO->reference = array_pop($arrInputs);
      }else{
         $txDTO->reference = $txDTO->mobileNumber;
      }
      array_pop($arrInputs);

      if($selectedMenu->parent_id == '0'){
         array_splice($arrInputs,1,0,$selectedMenu->order);
      }else{
         while ($selectedMenu->parent_id != '0') {
            array_splice($arrInputs,1,0,(string)$selectedMenu->order);
            $selectedMenu = $this->clientMenuService->findById($selectedMenu->parent_id);
         }
      }

      $arrInputs[] = $txDTO->customerAccount;
      $arrInputs[] = $txDTO->reference;
      $txDTO->customerJourney = implode( "*" ,$arrInputs);

      $walletStatus = $this->checkPaymentsEnabled->handle($txDTO);
      if(!$walletStatus['enabled']){
         throw new Exception($walletStatus['responseText'], 2);
      }

      return $txDTO;
       
   }

}