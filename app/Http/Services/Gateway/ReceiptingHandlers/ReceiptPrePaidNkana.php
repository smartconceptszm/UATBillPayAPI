<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\DTOs\BaseDTO;

class ReceiptPrePaidNkana implements IReceiptPayment
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		if($paymentDTO->tokenNumber == '' && $paymentDTO->paymentStatus == PaymentStatusEnum::NoToken->value){		
			//receiptNumber = transactionid in Nkana PrePaid Billing Client
			$receiptNumber = \now()->timestamp.\strtoupper(Str::random(6));
			$tokenParams = [
								'customerAccount'=>$paymentDTO->customerAccount,
								"paymentAmount" => $paymentDTO->receiptAmount,
								"transactionId" => $receiptNumber,
								'client_id'=>$paymentDTO->client_id
							];

			$tokenResponse=$this->billingClient->generateToken($tokenParams);

			if($tokenResponse['status']=='SUCCESS'){
				$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
				$paymentDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$paymentDTO->receiptNumber =  $receiptNumber;
				$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc/Meter No: " . $paymentDTO->customerAccount . "\n" .
											"Token: ". $paymentDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->error = $tokenResponse['error'];
			}
			
		}
		
		return $paymentDTO;

	}

}