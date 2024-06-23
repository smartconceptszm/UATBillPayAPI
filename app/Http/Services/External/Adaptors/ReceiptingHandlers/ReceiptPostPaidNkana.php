<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\NkanaPostPaid;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidNkana implements IReceiptPayment
{

	public function __construct(
		private NkanaPostPaid $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$newBalance = "0";
		if($paymentDTO->customer){
			$newBalance = (float)(\str_replace(",", "", $paymentDTO->customer['balance'])) - 
										(float)$paymentDTO->receiptAmount;
			$newBalance = \number_format($newBalance, 2, '.', ',');
		}

		$receiptingParams = [
									'Payment_Provider'=>$paymentDTO->walletHandler,
									'Client_Ref_number' => $paymentDTO->ppTransactionId,
									'custkey' => $paymentDTO->accountNumber,
									'Amount' => $paymentDTO->receiptAmount,
									'client_id'=>$paymentDTO->client_id
							];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
				$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = 'RECEIPTED';
				$paymentDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->accountNumber . "\n";
				if($newBalance!=="0"){
					$paymentDTO->receipt .= "Bal: ZMW ".$newBalance . "\n";
				}
				$paymentDTO->receipt .= "Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$paymentDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}