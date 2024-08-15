<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\DTOs\BaseDTO;

class MenuSpoofer
{
   
   public function handle(BaseDTO $txDTO, object $selectedMenu)
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      $txDTO->customerJourney = $arrInputs[0]; 
      $txDTO->subscriberInput = $arrInputs[1]; 
      $txDTO->billingClient = $selectedMenu->billingClient; 
      $txDTO->menuPrompt = $selectedMenu->prompt;
      $txDTO->handler = $selectedMenu->handler; 
      $txDTO->menu_id = $selectedMenu->id; 
      return $txDTO;
       
   }

}