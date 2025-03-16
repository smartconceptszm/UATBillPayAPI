<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_LogStatusAll extends EfectivoPipelineContract
{
	
	protected function stepProcess(BaseDTO $paymentDTO)
	{

		try {
			$logMessage = sprintf(
						'(%s)%s Payment Status: %s (via %s). Transaction Id = %s. Session: %s. Channel: %s. Wallet: %s',
						$paymentDTO->urlPrefix,
						empty($paymentDTO->error)?'':$paymentDTO->error.'.',
						$paymentDTO->paymentStatus,
						$paymentDTO->walletHandler,
						$paymentDTO->transactionId,
						$paymentDTO->sessionId,
						$paymentDTO->channel,
						$paymentDTO->walletNumber
					);
			if (empty($paymentDTO->error)) {
				Log::info($logMessage);
			} else {
				Log::error($logMessage);
			}
			
		} catch (\Throwable $e) {
			$paymentDTO->error = 'At logging transaction. ' . $e->getMessage();
		}
		return $paymentDTO;

	}

}