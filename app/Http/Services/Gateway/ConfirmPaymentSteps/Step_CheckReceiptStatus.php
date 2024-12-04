<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_CheckReceiptStatus extends EfectivoPipelineContract
{

	public function __construct(
		private PaymentService $paymentService)
	{}

	protected function stepProcess(BaseDTO $paymentDTO)
	{
		
		try {
			if($paymentDTO->error == ''){
				$payment = $this->paymentService->findById($paymentDTO->id);
				if($payment->receiptNumber != ''){
					$paymentDTO->paymentStatus = $payment->paymentStatus;
					$paymentDTO->receiptNumber = $payment->receiptNumber;
					$paymentDTO->receipt = $payment->receipt;
					$paymentDTO->error = 'Payment already receipted - Session: '.$paymentDTO->sessionId; 
				}
			}
		} catch (\Throwable $e) {
			$paymentDTO->error='At check payment receipt status. '.$e->getMessage();
		}
		return $paymentDTO;

	}
}