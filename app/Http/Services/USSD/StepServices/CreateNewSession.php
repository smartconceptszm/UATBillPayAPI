<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Sessions\ShortcutCustomerService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Sessions\SessionService;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class CreateNewSession
{

   public function __construct(
      private ShortcutCustomerService $shortcutCustomerService,
      private ClientMenuService $clientMenuService,
      private SessionService $sessionService)
   {}
   
   public function handle(BaseDTO $txDTO)
   {

      $homeMenu = $this->clientMenuService->findOneBy([
                                 'client_id' => $txDTO->client_id,
                                 'parent_id' =>'0'
                              ]);
      $txDTO->billingClient = $homeMenu->billingClient; 
      $txDTO->menuPrompt = $homeMenu->prompt;
      $txDTO->handler = $homeMenu->handler; 
      $txDTO->menu_id = $homeMenu->id; 
      $ussdSession = $this->sessionService->create($txDTO->toSessionData());
      $txDTO->id = $ussdSession->id;
      if(\count(\explode("*", $txDTO->subscriberInput))>1){
         $txDTO = $this->handleShortcut($txDTO, $homeMenu);
      }
      return $txDTO;
      
   }

   private function handleShortcut(BaseDTO $txDTO, object $homeMenu):BaseDTO
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      $selectedMenu = $this->clientMenuService->findOneBy([
                                                   'order' => $arrInputs[1],
                                                   'client_id' => $txDTO->client_id,
                                                   'parent_id' => $homeMenu->id,
                                                   'isActive' => 'YES'
                                                ]);
      if($selectedMenu && $selectedMenu->shortcut){
         $shortCut = App::make($selectedMenu->shortcut);
         $txDTO = $shortCut->handle($txDTO, $selectedMenu);
      }else{
         $txDTO->subscriberInput = $txDTO->shortCode;
      }
      return $txDTO;
       
   }

}