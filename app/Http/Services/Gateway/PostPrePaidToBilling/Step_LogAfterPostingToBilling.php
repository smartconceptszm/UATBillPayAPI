<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_LogAfterPostingToBilling extends EfectivoPipelineContract
{
	protected function stepProcess(BaseDTO $paymentDTO)
	{
		try {
			if (empty($paymentDTO->error)) {
				$logMessage = $this->buildLogMessage($paymentDTO);
				Log::info($logMessage);
				if ($paymentDTO->sms) {
					$this->logSMSStatus($paymentDTO);
				}
			} else {
				$this->logError($paymentDTO);
			}
		} catch (\Throwable $e) {
			$paymentDTO->error = 'At logging transaction. ' . $e->getMessage();
		}

		return $paymentDTO;
	}

	private function buildLogMessage(BaseDTO $paymentDTO): string
	{
		$logMessage = sprintf(
										'(%s) Payment posted to Billing System. Status: %s (via %s). Transaction ID = %s. Session: %s. Channel: %s. Wallet: %s',
										$paymentDTO->urlPrefix,
										$paymentDTO->paymentStatus,
										$paymentDTO->walletHandler,
										$paymentDTO->transactionId,
										$paymentDTO->sessionId,
										$paymentDTO->channel,
										$paymentDTO->walletNumber
									);

		if (in_array($paymentDTO->paymentStatus, [
			PaymentStatusEnum::Paid->value,
			PaymentStatusEnum::Receipted->value,
			PaymentStatusEnum::Receipt_Delivered->value
		])) {
			$logMessage .= ' DETAILS: ' . $paymentDTO->receipt;
		}

		return $logMessage;
	}

	private function logSMSStatus(BaseDTO $paymentDTO): void
	{
		$smsStatus = $paymentDTO->sms['status'];
		$logMessage = sprintf(
			'(%s) SMS Notification %s. Session: %s. Phone: %s',
			$paymentDTO->urlPrefix,
			$smsStatus === 'DELIVERED' ? 'SENT' : 'NOT SENT - ' . $paymentDTO->sms['error'],
			$paymentDTO->sessionId,
			$paymentDTO->mobileNumber
		);

		if ($smsStatus === 'DELIVERED') {
			Log::info($logMessage);
		} else {
			Log::error($logMessage);
		}
	}

	private function logError(BaseDTO $paymentDTO): void
	{
		$logMessage = sprintf(
			'(%s) Posting payment to billing: %s. Payment Status: %s (via %s). Transaction ID = %s. Session: %s. Channel: %s. Wallet: %s',
			$paymentDTO->urlPrefix,
			$paymentDTO->error,
			$paymentDTO->paymentStatus,
			$paymentDTO->walletHandler,
			$paymentDTO->transactionId,
			$paymentDTO->sessionId,
			$paymentDTO->channel,
			$paymentDTO->walletNumber
		);

		Log::error($logMessage);
	}

}