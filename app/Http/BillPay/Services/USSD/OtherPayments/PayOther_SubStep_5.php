<?php

namespace App\Http\BillPay\Services\USSD\OtherPayments;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_ConfirmToPay;

use App\Http\BillPay\DTOs\BaseDTO;

class PayOther_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   private $confirmToPay;
   public function __construct(StepService_ConfirmToPay $confirmToPay){
       $this->confirmToPay=$confirmToPay;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
         $txDTO->stepProcessed=true;
         try {
            $txDTO=$this->confirmToPay->handle($txDTO);
         } catch (\Throwable $e) {
            $txDTO->errorType = 'InvalidConfimation';
            $txDTO->error = $e->getMessage();
            $txDTO->subscriberInput = $txDTO->customerJourney;
            $txDTO->customerJourney = '';
            return $txDTO;
         }
         $txDTO->response= \strtoupper($txDTO->urlPrefix).
                                 " Payment request submitted to ".$txDTO->mnoName."\n".
                                       "You will receive a PIN prompt shortly!"."\n\n";
      }
      return $txDTO;
      
   }

}