<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\NkanaPrePaid;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\DTOs\BaseDTO;

class ReceiptPrePaidNkana implements IReceiptPayment
{

	public function __construct(
		private NkanaPrePaid $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		if(!$paymentDTO->tokenNumber){
			$paymentDTO->paymentStatus = "PAID | NO TOKEN";
			//receiptNumber = transactionid in Nkana PrePaid Billing Client
			$paymentDTO->receiptNumber =  \now()->timestamp.\strtoupper(Str::random(6));
			$tokenParams = [
								"paymentAmount" => $paymentDTO->receiptAmount,
								"transactionId" => $paymentDTO->receiptNumber,
								"meterNumber"=> $paymentDTO->meterNumber,
								'client_id'=>$paymentDTO->client_id
							];

			$tokenResponse=$this->billingClient->generateToken($tokenParams);

			if($tokenResponse['status']=='SUCCESS'){
				$paymentDTO->paymentStatus = "RECEIPTED";
				$paymentDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$paymentDTO->receipt = "Payment successful\n" .
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Meter No: " . $paymentDTO->meterNumber . "\n" .
											"Acc: " . $paymentDTO->accountNumber . "\n".
											"Token: ". $paymentDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->receiptNumber = '';
				$paymentDTO->error = $tokenResponse['error'];
			}
			
		}


		return $paymentDTO;

	}

}