<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\SMS\SMSService;
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;

class Step_SendReceiptViaSMS extends EfectivoPipelineContract
{

   public function __construct(
      private SMSService $smsService,
      private SMSTxDTO $smsTxDTO)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if($paymentDTO->paymentStatus == 'RECEIPTED' || $paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED'){
            if($paymentDTO->paymentStatus != 'RECEIPTED'){
               $paymentDTO->receipt = "Payment received BUT NOT receipted." . "\n" . 
                              \strtoupper($paymentDTO->urlPrefix).
                              " will receipt the payment shortly, please wait.\n";
            }
            $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromPaymentDTO($paymentDTO));
            $paymentDTO->sms['status'] = $this->smsTxDTO->status;
            $paymentDTO->sms['error'] = $this->smsTxDTO->error;
            if($this->smsTxDTO->status == 'DELIVERED' && $paymentDTO->paymentStatus == "RECEIPTED"){
               $paymentDTO->paymentStatus = "RECEIPT DELIVERED";
            }
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At receipt via sms. '.$e->getMessage();
      }
      return $paymentDTO;
   }

}