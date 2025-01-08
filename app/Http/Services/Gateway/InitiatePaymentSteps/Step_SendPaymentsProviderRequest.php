<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class Step_SendPaymentsProviderRequest extends EfectivoPipelineContract
{
    public function __construct(
        private IPaymentsProviderClient $paymentsProviderClient
    ) {}

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {
            if (empty($paymentDTO->error)) {
                $paymentsProviderResponse = $this->paymentsProviderClient->requestPayment($paymentDTO->toProviderParams());
                $this->updatePaymentStatus($paymentDTO, $paymentsProviderResponse);
            }
        } catch (\Throwable $e) {
            $paymentDTO->error = 'At send momo request. ' . $e->getMessage();
        }
        return $paymentDTO;
    }

    private function updatePaymentStatus(BaseDTO $paymentDTO, $paymentsProviderResponse): void
    {
        $paymentDTO->transactionId = $paymentsProviderResponse->transactionId;

        if ($paymentsProviderResponse->status === 'SUBMITTED') {
            $paymentDTO->paymentStatus = PaymentStatusEnum::Submitted->value;
        } else {
            $paymentDTO->paymentStatus = PaymentStatusEnum::Submission_Failed->value;
            $paymentDTO->error = $paymentsProviderResponse->error;
        }
    }
}
