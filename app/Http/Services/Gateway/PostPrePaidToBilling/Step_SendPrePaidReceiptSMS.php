<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Enums\PaymentTypeEnum;
use App\Http\Services\SMS\SMSService;
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;

class Step_SendPrePaidReceiptSMS extends EfectivoPipelineContract
{
   public function __construct(
      private ClientMenuService $clientMenuService,
      private SMSService $smsService,
      private SMSTxDTO $smsTxDTO
   ) {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      try {
         if ($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value) {
            $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromPaymentDTO($paymentDTO));
            $paymentDTO->sms['status'] = $this->smsTxDTO->status;
            $paymentDTO->sms['error'] = $this->smsTxDTO->error;
            if ($this->smsTxDTO->status === 'DELIVERED') {
               $paymentDTO->paymentStatus = PaymentStatusEnum::Receipt_Delivered->value;
            }
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At receipt via SMS. ' . $e->getMessage();
      }

      return $paymentDTO;
   }

}
