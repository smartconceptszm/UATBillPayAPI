<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Enums\PaymentTypeEnum;
use App\Http\DTOs\BaseDTO;

class Step_GetPaymentStatus extends EfectivoPipelineContract
{
    public function __construct(
        private IPaymentsProviderClient $paymentsProviderClient,
        private ClientMenuService $clientMenuService
    ) {}

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {
            if ($this->isStatusCheckRequired($paymentDTO->paymentStatus)) {
                $paymentsProviderResponse = $this->paymentsProviderClient->confirmPayment($paymentDTO->toProviderParams());
                $this->updatePaymentStatus($paymentDTO, $paymentsProviderResponse);
            }
        } catch (\Throwable $e) {
            $paymentDTO->error = 'At get payment status pipeline step. ' . $e->getMessage();
        }

        return $paymentDTO;
    }

    private function isStatusCheckRequired(string $paymentStatus): bool
    {
        return in_array($paymentStatus, [
            PaymentStatusEnum::Submitted->value,
            PaymentStatusEnum::Payment_Failed->value,
            PaymentStatusEnum::Submission_Failed->value,
        ]);
    }

    private function updatePaymentStatus(BaseDTO $paymentDTO, $paymentsProviderResponse): void
    {
        if ($paymentsProviderResponse->status === "PAYMENT SUCCESSFUL") {
            $this->handleSuccessfulPayment($paymentDTO, $paymentsProviderResponse->ppTransactionId);
        } else {
            $this->handleFailedPayment($paymentDTO, $paymentsProviderResponse->error);
        }
    }

    private function handleSuccessfulPayment(BaseDTO $paymentDTO, string $ppTransactionId): void
    {
        $paymentDTO->ppTransactionId = $ppTransactionId;
        $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);

        $paymentDTO->paymentStatus = ($theMenu->paymentType === PaymentTypeEnum::PrePaid->value)
            ? PaymentStatusEnum::NoToken->value
            : PaymentStatusEnum::Paid->value;
    }

    private function handleFailedPayment(BaseDTO $paymentDTO, string $error): void
    {
        $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
        $paymentDTO->error = $error;
    }
}
