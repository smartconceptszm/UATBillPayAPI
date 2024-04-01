<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPaymentMock implements IReceiptPayment
{

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		$newBalance="0";
		$momoDTO->receiptNumber = "RCPT".\rand(1000,100000);
		$momoDTO->paymentStatus = "RECEIPTED";
		$momoDTO->receipt = "Payment successful\n" .
									"Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
									"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
									"Acc: " . $momoDTO->accountNumber."\n";
		if($newBalance!="0"){
			$momoDTO->receipt.="Bal: ZMW ".$newBalance . "\n";
		}
		$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
		return $momoDTO;

	}

}