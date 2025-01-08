<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Payments\PaymentService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{
    public function __construct(
        private ClientMenuService $clientMenuService,
        private PaymentService $paymentService,
        private IReceiptPayment $receiptPayment
    ) {}

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {
            if ($this->isPaymentEligibleForReceipt($paymentDTO->paymentStatus)) {
                $this->processReceipt($paymentDTO);
            }
        } catch (\Throwable $e) {
            $paymentDTO->error = 'At post payment step. ' . $e->getMessage();
        }

        return $paymentDTO;
    }

    private function isPaymentEligibleForReceipt(string $paymentStatus): bool
    {
        return in_array($paymentStatus, [
            PaymentStatusEnum::Paid->value,
            PaymentStatusEnum::NoToken->value,
        ]);
    }

    private function processReceipt(BaseDTO $paymentDTO): void
    {
        if (!empty($paymentDTO->receiptNumber)) {
            $paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
        } else {
            $paymentDTO = $this->receiptPayment->handle($paymentDTO);
        }
    }
}
