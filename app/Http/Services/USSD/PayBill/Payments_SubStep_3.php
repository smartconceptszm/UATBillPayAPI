<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\Services\MoMo\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_GetAmount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class Payments_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts,
      private GetCustomerAccount $getCustomerAccount,
      private StepService_GetAmount $getAmount, 
   ){}

   public function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
         $txDTO->stepProcessed=true;
         try {
            $txDTO->subscriberInput = $this->getAmount->handle($txDTO->subscriberInput,
                  $txDTO->urlPrefix, $txDTO->mobileNumber);
         } catch (Exception $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAmount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }
         
         try {
               $txDTO->customer = $this->getCustomerAccount->handle($txDTO->accountNumber,$txDTO->urlPrefix);
               $txDTO->district = $txDTO->customer['district'];
         } catch (Exception$e) {
               if($e->getCode()==1){
                  $txDTO->errorType = 'InvalidAccount';
               }else{
                  $txDTO->errorType = 'SystemError';
               }
               $txDTO->error=$e->getMessage();
               return $txDTO;
         }
         $txDTO = $this->getResponse($txDTO);
      }
      return $txDTO;
      
   }

   private function getResponse(BaseDTO $txDTO): BaseDTO
   {

      $txDTO->response= "Pay ZMW ".$txDTO->subscriberInput." into:\n";
      $txDTO->response .= "Acc: ".$txDTO->accountNumber."\n".
                  "Name: ".$txDTO->customer['name']."\n";
      if($txDTO->clientSurcharge!='YES'){
         $txDTO->response .= "Addr: ".$txDTO->customer['address']."\n". 
         "Bal: ".$txDTO->customer['balance']."\n\n";
      }else{
         $paymentAmounts = $this->calculatePaymentAmounts->handle(
                                          $txDTO->urlPrefix,$txDTO->mno_id,$txDTO->suscriberInput);
         $txDTO->response .= "\nYou will be surcharged ZMW "
                     .number_format($paymentAmounts['paymentAmount'] -
                                 $paymentAmounts['receiptAmount'], 2, '.', ',')
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