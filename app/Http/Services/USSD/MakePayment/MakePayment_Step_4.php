<?php

namespace App\Http\Services\USSD\MakePayment;

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\EnquiryHandler;
use App\Http\Services\Gateway\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\USSD\StepServices\GetAmount;
use App\Http\Services\Web\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class MakePayment_Step_4
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts,
      private ClientMenuService $clientMenuService,
      private GetAmount $getAmount,
      private EnquiryHandler $enquiryHandler 
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
         $txDTO->error = 'Make payment step 4. '. $e->getMessage();
         return $txDTO;
      }

      $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
      if($clientMenu->onOneAccount == 'NO'){
         try {
            $txDTO = $this->enquiryHandler->handle($txDTO);
         } catch (\Throwable$e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = 'Make payment step 4. '.$e->getMessage();
            return $txDTO;
         }
      }

      $txDTO = $this->getResponse($txDTO,$clientMenu);
      return $txDTO;
      
   }

   private function getResponse(BaseDTO $txDTO, object $clientMenu): BaseDTO
   {

      $txDTO->response = "Pay ZMW ".$txDTO->subscriberInput."\n";
      if($clientMenu->onOneAccount == "NO"){
         $txDTO->response .= " into: ".$txDTO->customerAccount." - ".$txDTO->customer['name']."\n";
      }else{
         $txDTO->response .= " for: ". $clientMenu->prompt."\n";
      }
      if($clientMenu->requiresReference == 'YES' && $txDTO->reference){
         $txDTO->response .= "Ref: ".$txDTO->reference."\n";
      }
      if($txDTO->clientSurcharge!='YES' ){ 
         if($txDTO->customer && $clientMenu->onOneAccount == "NO"){
            $txDTO->response .= "Addr: ".$txDTO->customer['address']."\n". 
                                       "Bal: ".$txDTO->customer['balance']."\n\n";
         }
      }else{
         $txDTO = $this->calculatePaymentAmounts->handle($txDTO);
         $txDTO->response .= "\nYou will be surcharged ZMW "
                     .number_format($txDTO->paymentAmount -
                                             $txDTO->receiptAmount, 2, '.', ',')
                     ." for this transaction\n\n";
      }
      $txDTO->response .= "Enter\n". 
                           "1. Confirm\n".
                           "0. Back";
                           
      $cacheValue = \json_encode([
               'must'=>false,
               'steps'=>2,
         ]);                    
      Cache::put($txDTO->sessionId."handleBack",$cacheValue, 
         Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
      return $txDTO;

   }

}