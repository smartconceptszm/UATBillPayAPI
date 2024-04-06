<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\NkanaPostPaid;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidNkana implements IReceiptPayment
{

	public function __construct(
		private NkanaPostPaid $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		$newBalance = "0";
		if($momoDTO->customer){
			$newBalance = (float)(\str_replace(",", "", $momoDTO->customer['balance'])) - 
										(float)$momoDTO->receiptAmount;
			$newBalance = \number_format($newBalance, 2, '.', ',');
		}

		$receiptingParams=[
				'custkey' => $momoDTO->accountNumber,
				'Client_Ref_number' => $momoDTO->mnoTransactionId,
				'Amount' => $momoDTO->receiptAmount,
				'Payment_Provider'=>$momoDTO->mnoName,
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
				$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
				$momoDTO->paymentStatus = 'RECEIPTED';
				$momoDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $momoDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $momoDTO->accountNumber . "\n";
				if($newBalance!=="0"){
					$momoDTO->receipt .= "Bal: ZMW ".$newBalance . "\n";
				}
				$momoDTO->receipt .= "Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $momoDTO;

	}

}