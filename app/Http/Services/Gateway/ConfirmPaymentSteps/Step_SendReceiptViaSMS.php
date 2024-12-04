<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
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
         if($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value ||
                                          PaymentStatusEnum::Receipt_Delivered->value ||
                                           $paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value){

            if($paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value){
               $paymentDTO->receipt = "Payment received BUT NOT receipted." . "\n" . 
                              \strtoupper($paymentDTO->urlPrefix).
                              " will receipt the payment shortly, please wait.\n";
            }
            $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromPaymentDTO($paymentDTO));
            $paymentDTO->sms['status'] = $this->smsTxDTO->status;
            $paymentDTO->sms['error'] = $this->smsTxDTO->error;
            if($this->smsTxDTO->status == 'DELIVERED' && $paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value){
               $paymentDTO->paymentStatus = PaymentStatusEnum::Receipt_Delivered->value;
            }
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At receipt via sms. '.$e->getMessage();
      }
      return $paymentDTO;
   }

}