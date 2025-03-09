<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_CheckReceiptStatus extends EfectivoPipelineContract
{
    public function __construct(private PaymentService $paymentService) {}

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {

            $payment = $this->paymentService->findById($paymentDTO->id);
            if ($this->hasReceipt($payment)) {
                $this->updatePaymentDTO($paymentDTO, $payment);
            }
            
        } catch (\Throwable $e) {
            $paymentDTO->error = 'At check payment receipt status. ' . $e->getMessage();
        }

        return $paymentDTO;
    }

    private function hasReceipt($payment): bool
    {
        return !empty($payment->receiptNumber);
    }

    private function updatePaymentDTO(BaseDTO $paymentDTO, $payment): void
    {
        $paymentDTO->paymentStatus = $payment->paymentStatus;
        $paymentDTO->receiptNumber = $payment->receiptNumber;

        if (!empty($payment->receipt)) {
            $paymentDTO->receipt = $payment->receipt;
        }
    }
}