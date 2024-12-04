<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class MakeCouncilPayment
{

   public function __construct(
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ClientMenuService $clientMenuService)
   {}
   
   public function handle(BaseDTO $txDTO, $selectedMenu)
   {

      $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
      if(!$momoPaymentStatus['enabled']){
         throw new Exception($momoPaymentStatus['responseText'], 2);
      }

      $arrInputs = explode("*", $txDTO->subscriberInput);
      if(count($arrInputs) > 2){
         $selectedMenu = $this->clientMenuService->findOneBy([
                                          'order' => $arrInputs[2],
                                          'client_id' => $txDTO->client_id,
                                          'parent_id' => $selectedMenu->id,
                                          'isActive' => 'YES'
                                       ]);
         if(!$selectedMenu ){
            $txDTO->subscriberInput = $txDTO->shortCode;
            return $txDTO;
         }
      }

      $txDTO->customer['customerAccount'] = $selectedMenu->commonAccount;
      $txDTO->customerAccount = $selectedMenu->commonAccount;
      $txDTO->customer['name'] = $selectedMenu->description;
      $txDTO->billingClient = $selectedMenu->billingClient; 
      $txDTO->menuPrompt = $selectedMenu->prompt;
      $txDTO->handler = $selectedMenu->handler; 
      $txDTO->menu_id = $selectedMenu->id; 

      $txDTO->subscriberInput = array_pop($arrInputs);
      if(count($arrInputs) > 2){
         array_splice($arrInputs,3,0,$selectedMenu->commonAccount);
      }
      $txDTO->customerJourney = implode( "*" ,$arrInputs);

      return $txDTO;
       
   }

}