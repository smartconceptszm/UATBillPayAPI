<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_CheckReceiptStatus extends EfectivoPipelineContract
{

	public function __construct(
		private PaymentService $paymentService)
	{}

	protected function stepProcess(BaseDTO $momoDTO)
	{
		try {
			if($momoDTO->error == ''){
					$payment = $this->paymentService->findById($momoDTO->id);
					if($payment->receiptNumber != ''){
						$momoDTO->paymentStatus=$payment->paymentStatus;
						$momoDTO->receiptNumber=$payment->receiptNumber;
						$momoDTO->receipt=$payment->receipt;
						$momoDTO->error = 'Payment already receipted - Session: '.$momoDTO->sessionId; 
					}
			}
		} catch (\Throwable $e) {
			$momoDTO->error='At check payment receipt status. '.$e->getMessage();
		}
		return $momoDTO;

	}
}