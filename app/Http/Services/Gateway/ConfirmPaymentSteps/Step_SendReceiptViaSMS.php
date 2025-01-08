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
      private SMSTxDTO $smsTxDTO
   ) {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      try {
         if ($this->isPaymentStatusEligibleForSMS($paymentDTO->paymentStatus)) {
               $this->handleReceiptMessage($paymentDTO);
               $this->sendSMS($paymentDTO);
               $this->updatePaymentStatusAfterSMS($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At receipt via SMS. ' . $e->getMessage();
      }

      return $paymentDTO;
   }

   private function isPaymentStatusEligibleForSMS(string $paymentStatus): bool
   {
      return in_array($paymentStatus, [
         PaymentStatusEnum::Paid->value,
         PaymentStatusEnum::NoToken->value,
         PaymentStatusEnum::Receipted->value,
         PaymentStatusEnum::Receipt_Delivered->value,
      ]);
   }

   private function handleReceiptMessage(BaseDTO $paymentDTO): void
   {
      if (in_array($paymentDTO->paymentStatus, [PaymentStatusEnum::Paid->value, PaymentStatusEnum::NoToken->value])) {
         $paymentDTO->receipt = "Payment received BUT NOT receipted.\n" .
               strtoupper($paymentDTO->urlPrefix) .
               " will receipt the payment shortly, please wait.\n";
      }
   }

   private function sendSMS(BaseDTO $paymentDTO): void
   {
      $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromPaymentDTO($paymentDTO));
      $paymentDTO->sms['status'] = $this->smsTxDTO->status;
      $paymentDTO->sms['error'] = $this->smsTxDTO->error;
   }

   private function updatePaymentStatusAfterSMS(BaseDTO $paymentDTO): void
   {
      if ($this->smsTxDTO->status === 'DELIVERED') {
         $paymentDTO->paymentStatus = PaymentStatusEnum::Receipt_Delivered->value;
      }
   }
}
