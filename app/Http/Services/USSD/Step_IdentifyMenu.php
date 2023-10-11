<?php

namespace App\Http\Services\USSD;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\USSD\ShortcutCustomerService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\USSD\SessionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_IdentifyMenu extends EfectivoPipelineContract
{

   private $handleBack;

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private ShortcutCustomerService $shortcutCustomerService,
      private ClientMenuService $clientMenuService,
      private SessionService $sessionService)
   {}
   
   protected function stepProcess(BaseDTO $txDTO)
   {

      if($txDTO->error == ''){ 
         try {
            //Get menu from Session
            if($txDTO->isNewRequest == '1'){
               $txDTO = $this->newSession($txDTO);
            }else{
               $txDTO = $this->existingSession($txDTO);
            }
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->error = 'At identify menu. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
            }
         }
      }
      App::bind(\App\Http\Services\USSD\Menus\IUSSDMenu::class,$txDTO->handler);
      return $txDTO;
      
   }

   private function newSession(BaseDTO $txDTO)
   {

      $selectedMenu = $this->clientMenuService->findOneBy([
                              'client_id' => $txDTO->client_id,
                              'parent_id' => 0
                           ]);

      if(\count(\explode("*", $txDTO->subscriberInput))>1){
         $txDTO = $this->handleShortcut($txDTO, $selectedMenu);
      }else{
         $txDTO->menu_id = $selectedMenu->id; 
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->menuPrompt = $selectedMenu->prompt; 
         $txDTO->isPaymentMenu = $selectedMenu->isPayment; 
      }

      $ussdSession = $this->sessionService->create($txDTO->toSessionData());
      $txDTO->id = $ussdSession->id;
      return $txDTO;

   }

   private function handleShortcut(BaseDTO $txDTO, object $homeMenu):BaseDTO
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      $customer = $this->shortcutCustomerService->findOneBy([
                     'mobileNumber' => $txDTO->mobileNumber,
                     'client_id' => $txDTO->client_id
                  ]);
      if ($customer->accountNumber) { 
         $selectedMenu = $this->clientMenuService->findOneBy([
                              'order' => $txDTO->$arrInputs[1],
                              'client_id' => $txDTO->client_id,
                              'parent_id' => $homeMenu->id,
                              'isActive' => "YES"
                           ]);
         if($selectedMenu){
            $txDTO->menu_id = $selectedMenu->id; 
            $txDTO->handler = $selectedMenu->handler; 
            $txDTO->menuPrompt = $selectedMenu->prompt; 
            $txDTO->isPaymentMenu = $selectedMenu->isPayment; 
            if($selectedMenu->isPayment == 'YES'){
               $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
               if(!$momoPaymentStatus['enabled']){
                  $txDTO->response = $momoPaymentStatus['responseText'];
                  $txDTO->lastResponse= true;
                  return $txDTO;
               }
            }
            if (\count($arrInputs) == 2) {
               $txDTO->subscriberInput = $customer->accountNumber;
               $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1];
               return $txDTO;
            }
            if (\count($arrInputs) == 3) {
               $txDTO->subscriberInput = $arrInputs[2];
               $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1] . "*" . $customer->accountNumber;
               return $txDTO;
            }
         }
      }
      $txDTO->subscriberInput = $arrInputs[0];
      $txDTO->customerJourney = '';
      return $txDTO;
       
   }

   private function existingSession(BaseDTO $txDTO)
   {

      $ussdSession = $this->sessionService->findOneBy([   
                                 'mobileNumber'=>$txDTO->mobileNumber,
                                 'client_id'=>$txDTO->client_id,
                                 'sessionId'=>$txDTO->sessionId,
                              ]);
      $txDTO->customerJourney = $ussdSession->customerJourney;
      $txDTO->accountNumber = $ussdSession->accountNumber;
      $txDTO->paymentAmount = $ussdSession->paymentAmount;
      $txDTO->district = $ussdSession->district;
      $txDTO->menu_id = $ussdSession->menu_id;
      $txDTO->mno_id = $ussdSession->mno_id;
      $txDTO->status = $ussdSession->status;
      $txDTO->id = $ussdSession->id;
      $txDTO->error = '';

      $currentMenu = $this->clientMenuService->findById($txDTO->menu_id);

      if($currentMenu->isParent == 'YES'){
         $selectedMenu = $this->clientMenuService->findOneBy([
                        'order' => $txDTO->subscriberInput,
                        'client_id' => $txDTO->client_id,
                        'parent_id' => $txDTO->menu_id,
                        'isActive' => "YES"
                     ]);
         if(!$selectedMenu){
            throw new Exception("Invalid Menu Item number", 1);
         }
         $txDTO->menu_id = $selectedMenu->id; 
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->menuPrompt = $selectedMenu->prompt; 
         $txDTO->isPaymentMenu = $selectedMenu->isPayment; 
      }else{
         $txDTO->isPaymentMenu = $currentMenu->isPayment; 
         $txDTO->menuPrompt = $currentMenu->prompt; 
         $txDTO->handler = $currentMenu->handler;
      }

      $this->handleBack = \json_decode(Cache::get($txDTO->sessionId."handleBack",''),true);
      if($this->handleBack){
         $txDTO = $this->handleBackStep($txDTO); 
      }
      return $txDTO;

   }

   private function handleBackStep(BaseDTO $txDTO)
   {

      Cache::forget($txDTO->sessionId."handleBack");
      $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
      $backSteps = $this->handleBack['steps'];
      if($txDTO->subscriberInput ==='0'){
         $txDTO->status = 'INITIATED';
         for ($i=1; $i <= $backSteps; $i++) { 
            if($arrCustomerJourney){
                  \array_pop($arrCustomerJourney);
            }
         }
         $responseNext = Cache::get($txDTO->sessionId."responseNext",'');
         if(!$responseNext){
            $txDTO=$this->resetCustomerJourney($txDTO,$arrCustomerJourney);
         }
         Cache::forget($txDTO->sessionId."responseNext");
      }else{
         if($this->handleBack['must'] && $txDTO->subscriberInput !='0'){
            $txDTO = $this->resetCustomerJourney($txDTO,$arrCustomerJourney);
            throw new Exception("Only expected input is 0!", 1);
         }
      }
      return $txDTO;

   }

   private function resetCustomerJourney(BaseDTO $txDTO, array $arrCustomerJourney): BaseDTO
   {

      if($arrCustomerJourney){
         $txDTO->subscriberInput = \end($arrCustomerJourney);
         if( \count($arrCustomerJourney) > 1){
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
            $txDTO->menu_id = $selectedMenu->id; 
            $txDTO->handler = $selectedMenu->handler; 
            $txDTO->menuPrompt = $selectedMenu->prompt; 
            $txDTO->customerJourney='';
         }
      }else{
         $txDTO->subscriberInput = \config('efectivo_clients.'.
                                          $txDTO->urlPrefix.'.shortCode');
         $selectedMenu = $this->clientMenuService->findOneBy([
                                 'client_id' => $txDTO->client_id,
                                 'parent_id' => 0
                              ]);
         $txDTO->menu_id = $selectedMenu->id; 
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->menuPrompt = $selectedMenu->prompt; 
         $txDTO->customerJourney = '';
      }
      return $txDTO;
      
   }


}