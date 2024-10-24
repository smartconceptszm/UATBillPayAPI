<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Clients\ClientRevenueCodeService;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_4
{

   public function __construct(
      private ClientRevenueCodeService $revenueCodeService,
      private ClientMenuService $clientMenuService,
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
      if($theMenu->onOneAccount =="YES"){
         $theService = "For: (".$theMenu->commonAccount.") - ".$parentMenu->prompt.": ".$theMenu->prompt."\n";
      }else{
         $revenueCode = $this->revenueCodeService->findOneBy(['menu_id' =>$txDTO->menu_id,
                                                                     'code' =>$txDTO->customerAccount]);
         $theService = "For: (".$revenueCode->code.") - ".$parentMenu->prompt.": ".$revenueCode->name."\n";
      }

      $txDTO->response = "Pay ZMW ".$txDTO->subscriberInput."\n";
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