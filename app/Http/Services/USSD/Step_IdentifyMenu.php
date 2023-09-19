<?php

namespace App\Http\Services\USSD;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\USSD\Menus\MenuBinderService;
use App\Http\Services\USSD\ShortcutCustomerService;
use App\Http\Services\USSD\SessionService;
use App\Http\Services\Clients\MnoService;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_IdentifyMenu extends EfectivoPipelineContract
{

   public function __construct(
      private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
      private ShortcutCustomerService $shortcutCustomerService,
      private MenuBinderService $menuBinderService, 
      private SessionService $sessionService, 
      private MnoService $mnoService)
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
            if (count(\explode("*", $txDTO->customerJourney))> 1) {
               $this->handleBack = \json_decode(Cache::get($txDTO->sessionId."handleBack",''),true);
               if($this->handleBack){
                  $txDTO = $this->backStep($txDTO); 
               }
            }
            if($txDTO->clean == "clean-session"){
               $txDTO->menu = 'Cleanup';
            }
            //Bind selected Menu Handler to the Interface
            $this->menuBinderService->bind($txDTO->menu);
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
      return $txDTO;
      
   }

   private function newSession(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->subscriberInput))>1){
         $txDTO = $this->handleShortcut($txDTO);
      }else{
         $txDTO->customerJourney = $txDTO->subscriberInput;
         $txDTO->menu = 'Home'; 
      }
      $mno = $this->mnoService->findOneBy(['name'=>$txDTO->mnoName]);               
      $txDTO->mno_id = $mno->id;
      $ussdSession = $this->sessionService->create($txDTO->toSessionData());
      $txDTO->id=$ussdSession->id;
      $txDTO->customerJourney = $txDTO->menu =='Home'? '': $txDTO->customerJourney;
      return $txDTO;

   }

   private function handleShortcut(BaseDTO $txDTO):BaseDTO
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      $customer = $this->shortcutCustomerService->findOneBy([
                     'mobileNumber'=>$txDTO->mobileNumber,
                     'client_id'=>$txDTO->client_id
                  ]);
      if ($customer->accountNumber) {                
         $arrConfig = \config('efectivo_clients');
         $arrMenus = $arrConfig[$txDTO->urlPrefix]['menu'];
         foreach ($arrMenus as $key => $value) {
            if($arrInputs[1] == $value){
                  $txDTO->menu = $key;
                  break;
            }
         }
         if($txDTO->menu!='Home'){
            if($txDTO->menu == 'PayBill' || $txDTO->menu == 'BuyUnits' || $txDTO->menu == 'OtherPayments'){
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
      $txDTO->district = $ussdSession->district;
      $txDTO->mno_id = $ussdSession->mno_id;
      $txDTO->status = $ussdSession->status;
      $txDTO->menu = $ussdSession->menu;
      $txDTO->id = $ussdSession->id;
      $txDTO->error = '';
      if (count(\explode("*", $txDTO->customerJourney))== 1) {
         //Check for valid menu selection
         $menuNo = (int)$txDTO->subscriberInput;
         $arrConfig=\config('efectivo_clients');
         $arrMenus=$arrConfig[$txDTO->urlPrefix]['menu'];
         if (($menuNo < 1) || ($menuNo > (\count($arrMenus)))){
            throw new Exception("Invalid Menu Item number", 1);
         }else{
            foreach ($arrMenus as $key => $value) {
                  if($txDTO->subscriberInput==$value){
                     $txDTO->menu=$key;
                     break;
                  }
            }
         }
      }
      return $txDTO;

   }

   private function backStep(BaseDTO $txDTO)
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
      }
      if($this->handleBack['must'] && $txDTO->subscriberInput !='0'){
         $txDTO = $this->resetCustomerJourney($txDTO,$arrCustomerJourney);
         throw new Exception("Only expected input is 0!", 1);
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
               $txDTO->menu='Home';
               $txDTO->customerJourney='';
         }
      }else{
         $txDTO->subscriberInput=\config('efectivo_clients.'.
                                          $txDTO->urlPrefix.'.shortCode');
         $txDTO->menu='Home';
         $txDTO->customerJourney='';
      }
      return $txDTO;
      
   }


}

