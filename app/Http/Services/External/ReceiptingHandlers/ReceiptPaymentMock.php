<?php

namespace App\Http\Services\External\ReceiptingHandlers;

use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPaymentMock implements IReceiptPayment
{

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$newBalance="0";
		$paymentDTO->receiptNumber = "RCPT".\rand(1000,100000);
		$paymentDTO->paymentStatus = "RECEIPTED";
		$paymentDTO->receipt = "\n"."Payment successful"."\n".
									"Rcpt No.: " . $paymentDTO->receiptNumber . "\n" .
									"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
									"Acc: " . $paymentDTO->customerAccount."\n";
		if($newBalance!="0"){
			$paymentDTO->receipt.="Bal: ZMW ".$newBalance . "\n";
		}
		$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
		return $paymentDTO;

	}

}