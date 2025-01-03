<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_4
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private EnquiryHandler $enquiryHandler,
      private GetAmount $getAmount
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {
         [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = 'InvalidAmount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council payment step 4. '. $e->getMessage();
         return $txDTO;
      }

      $theMenu = $this->clientMenuService->findById($txDTO->menu_id);
      $parentMenu = $this->clientMenuService->findById($theMenu->parent_id);
      $theService = '';
      $theAccount = '';
      if($theMenu->onOneAccount =="YES"){
         $theService = "For: (".$theMenu->commonAccount.") - ".$parentMenu->prompt.": ".$theMenu->prompt."\n";
      }else{
         try {
            $txDTO = $this->enquiryHandler->handle($txDTO);
            $theAccount = "Into: ".$txDTO->customerAccount." ".$txDTO->customer['name'];
            $theService = "For: ".$parentMenu->prompt.": ".$theMenu->prompt."\n";
         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }
      }

      $txDTO->response = "Pay ZMW ".$txDTO->subscriberInput."\n";
      if($theAccount != ''){
         $txDTO->response .= $theAccount;
      }
      $txDTO->response .= $theService;
      $txDTO->response .= "Ref: ".$txDTO->reference."\n";
      $txDTO->response .= "Enter\n". 
                           "1. Confirm\n".
                           "2. Use different wallet\n".
                           "0. Back";
                           
      $cacheValue = \json_encode([
               'must'=>false,
               'steps'=>2,
         ]);         
      $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
      Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
         Carbon::now()->addMinutes(intval($billpaySettings['SESSION_CACHE'])));

      return $txDTO;
      
   }

}