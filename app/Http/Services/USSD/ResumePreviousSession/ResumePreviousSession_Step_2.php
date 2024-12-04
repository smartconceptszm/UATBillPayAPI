<?php

namespace App\Http\Services\USSD\ResumePreviousSession;

use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class ResumePreviousSession_Step_2
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         
         if($txDTO->subscriberInput == '1'){
            $sessionToResume = (object)json_decode(cache($txDTO->sessionId."_Resume"),true);
            $arrCustomerJourney = explode('*',$sessionToResume->customerJourney);
            $subscriberInput = array_pop($arrCustomerJourney);
            if($subscriberInput == "-"){
               $subscriberInput = array_pop($arrCustomerJourney);
            }
            $menu = $this->clientMenuService->findById($sessionToResume->menu_id);
            $txDTO->customerAccount = $sessionToResume->customerAccount;
            $txDTO->customerJourney = implode('*',$arrCustomerJourney);
            $txDTO->paymentAmount = $sessionToResume->paymentAmount;
            $txDTO->paymentAmount = $sessionToResume->paymentAmount;
            $txDTO->revenuePoint = $sessionToResume->revenuePoint;
            $txDTO->menu_id = $sessionToResume->menu_id;
            $txDTO->status = $sessionToResume->status;
            $txDTO->subscriberInput = $subscriberInput;

            $txDTO->billingClient = $menu->billingClient; 
            $txDTO->menuPrompt = $menu->prompt;
            $txDTO->handler = $menu->handler; 

         }else{
            //Reset Customer Journey
            $homeMenu = $this->clientMenuService->findOneBy([
                                                      'client_id' => $txDTO->client_id,
                                                      'parent_id' =>'0'
                                                   ]);
            $txDTO->billingClient = $homeMenu->billingClient; 
            $txDTO->menuPrompt = $homeMenu->prompt;
            $txDTO->handler = $homeMenu->handler; 
            $txDTO->menu_id = $homeMenu->id; 
         }
         $menuHandler = App::make($txDTO->handler);
         $txDTO = $menuHandler->handle($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'Resume previous session step 2. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      cache()->forget($txDTO->sessionId."_Resume");
      return $txDTO;
      
   }

}