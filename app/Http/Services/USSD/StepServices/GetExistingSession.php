<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\USSD\StepServices\CreateNewSession;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Sessions\SessionService;
use App\Http\Services\Web\Clients\ClientService;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetExistingSession
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private CreateNewSession $newSession,
      private SessionService $sessionService,
      private ClientService $clientService)
   {}
   
   public function handle(BaseDTO $txDTO)
   {

      $ussdSession = $this->sessionService->findOneBy([   
                                                'mobileNumber'=>$txDTO->mobileNumber,
                                                'sessionId'=>$txDTO->sessionId,
                                             ]);

      if(!$ussdSession){
         $subscriberInput = \explode("*",$txDTO->subscriberInput);
         $txDTO->subscriberInput = $subscriberInput[0];
         $txDTO->isNewRequest = '1';
         return $this->newSession->handle($txDTO);
      }

      if($txDTO->ussdAggregator == 'YES'){
         $client = $this->clientService->findById($ussdSession->client_id);
         $txDTO->ussdAggregator = $client->ussdAggregator; 
         $txDTO->clientSurcharge = $client->surcharge; 
         $txDTO->testMSISDN = $client->testMSISDN;
         $txDTO->shortCode = $client->shortCode;
         $txDTO->urlPrefix = $client->urlPrefix;
         $txDTO->client_id = $client->id;
      }

      $txDTO->customerJourney = $ussdSession->customerJourney;
      $txDTO->customerAccount = $ussdSession->customerAccount;
      $txDTO->paymentAmount = $ussdSession->paymentAmount;
      $txDTO->district = $ussdSession->district;
      $txDTO->menu_id = $ussdSession->menu_id;
      $txDTO->mno_id = $ussdSession->mno_id;
      $txDTO->status = $ussdSession->status;
      $txDTO->id = $ussdSession->id;
      $txDTO->error = '';
      $currentMenu = $this->clientMenuService->findById($txDTO->menu_id);

      if($txDTO->subscriberInput === '00' && Cache::get($txDTO->sessionId."responseNext",'') ){
         $currentMenu->handler = 'NextPage';
      }

      if($currentMenu->handler == 'ParentMenu'){
         $currentMenu = $this->clientMenuService->findOneBy([
                              'order' => $txDTO->subscriberInput,
                              'client_id' => $txDTO->client_id,
                              'parent_id' => $txDTO->menu_id,
                              'isActive' => "YES"
                           ]);
         if(!$currentMenu){
            throw new Exception("Invalid Menu Item number", 1);
         }
      }

      $txDTO->billingClient = $currentMenu->billingClient; 
      $txDTO->menuPrompt = $currentMenu->prompt;
      $txDTO->handler = $currentMenu->handler; 
      $txDTO->menu_id = $currentMenu->id; 

      if(\json_decode(Cache::get($txDTO->sessionId."handleBack",''),true)){
         $txDTO = $this->handleBackStep($txDTO); 
      }

      return $txDTO;
      
   }

   private function handleBackStep(BaseDTO $txDTO)
   {

      $handleBack = \json_decode(Cache::get($txDTO->sessionId."handleBack",''),true);
      Cache::forget($txDTO->sessionId."handleBack");
      if($txDTO->subscriberInput ==='0' || $handleBack['must']){
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $backSteps = $handleBack['steps'];
         $txDTO->status = 'INITIATED';
         for ($i=1; $i <= $backSteps; $i++) { 
            if($arrCustomerJourney){
               \array_pop($arrCustomerJourney);
            }
         }
         if(!Cache::get($txDTO->sessionId."responseNext",'')){
            if( \count($arrCustomerJourney) > 1){
               $txDTO->subscriberInput = \end($arrCustomerJourney);
               \array_pop($arrCustomerJourney);
               if(\count($arrCustomerJourney)==1){
                  $txDTO->customerJourney =$arrCustomerJourney[0];
               }else{
                  $txDTO->customerJourney =\implode("*", $arrCustomerJourney);
               }
            }else{
               $selectedMenu = $this->clientMenuService->findOneBy([
                                                'client_id' => $txDTO->client_id,
                                                'parent_id' => 0
                                             ]);
               $txDTO->billingClient = $selectedMenu->billingClient; 
               $txDTO->menuPrompt = $selectedMenu->prompt;
               $txDTO->handler = $selectedMenu->handler; 
               $txDTO->menu_id = $selectedMenu->id;
               $txDTO->customerJourney = '';
            }
         }
         Cache::forget($txDTO->sessionId."responseNext");
      }
      return $txDTO;

   }

}