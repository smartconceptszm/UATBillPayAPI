<?php

namespace App\Http\Services\USSD\PayBusLevy;

use App\Http\Services\MoMo\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\USSD\Utility\StepService_GetAmount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBusLevy_Step_3
{

   public function __construct(
         private StepService_CalculatePaymentAmounts $calculatePaymentAmounts,
         private StepService_GetAmount $getAmount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $txDTO->reference = $arrCustomerJourney[2];
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = 'InvalidAmount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = $e->getMessage();
         return $txDTO;
      }
      $txDTO = $this->getResponse($txDTO);
      return $txDTO;
      
   }

   private function getResponse(BaseDTO $txDTO): BaseDTO
   {

      $txDTO->response= "Pay ZMW ".$txDTO->subscriberInput." as:\n";
      $txDTO->response .= "Bus levy for ".$txDTO->reference.".\n";
      if($txDTO->clientSurcharge == 'YES'){
         $calculatedAmounts = $this->calculatePaymentAmounts->handle($txDTO);
         $txDTO->response .= "\nYou will be surcharged ZMW "
                     .number_format($calculatedAmounts['paymentAmount'] -
                                 $calculatedAmounts['receiptAmount'], 2, '.', ',')
                     ." for this transaction\n";
      }
      $txDTO->response .= "\nEnter\n". 
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