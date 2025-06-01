<?php

namespace App\Http\Services\Promotions\PromotionHandlers;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class Step_LogStatusAll extends EfectivoPipelineContract
{
	
	protected function stepProcess(BaseDTO $promotionDTO)
	{

		try {
			$logMessage = sprintf(
						'(%s) Promotion Entered. Customer Account: (%s). Mobile Number (%4). Amount Paid (). Reward (%s)',
						$promotionDTO->urlPrefix,
						$promotionDTO->customerAccount,
						$promotionDTO->mobileNumber,
						$promotionDTO->paymentAmount,
						$promotionDTO->rewardAmount
					);
			if (empty($promotionDTO->error)) {
				Log::info($logMessage);
			} else {
				Log::error($promotionDTO->error);
			}
			
		} catch (\Throwable $e) {
			$promotionDTO->error = 'At logging transaction. ' . $e->getMessage();
		}
		return $promotionDTO;

	}

}