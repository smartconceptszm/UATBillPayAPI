<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_LogStatusInfoOnly extends EfectivoPipelineContract
{
	
	protected function stepProcess(BaseDTO $paymentDTO)
	{

		try {

			if (empty($paymentDTO->error)) {
				$logMessage = sprintf(
					'(%s) Payment Status: %s (via %s). Transaction Id = %s. Session: %s. Channel: %s. Wallet: %s',
					$paymentDTO->urlPrefix,
					$paymentDTO->paymentStatus,
					$paymentDTO->walletHandler,
					$paymentDTO->transactionId,
					$paymentDTO->sessionId,
					$paymentDTO->channel,
					$paymentDTO->walletNumber
				);
				Log::info($logMessage);
			}
			
		} catch (\Throwable $e) {
			$paymentDTO->error = 'At logging transaction status. ' . $e->getMessage();
		}
		return $paymentDTO;

	}

}