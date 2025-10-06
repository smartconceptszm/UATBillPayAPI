<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

abstract class AbrReceiptMulonga
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	protected function handleCommon(BaseDTO $paymentDTO, $newBalance,$receiptingParams):BaseDTO
	{

		$billingResponse=$this->billingClient->postPayment($receiptingParams);

		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
			$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;

			$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->customerAccount . "\n";
			if($newBalance != "0"){
				$paymentDTO->receipt.="Bal: ZMW ". $newBalance . "\n";
			}
			$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
			
		}else{
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}


}