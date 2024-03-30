<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\DTOs\BaseDTO;
use Exception;


class ReceiptPrePaidLukanga implements IReceiptPayment
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		// $newBalance = "0";
		// if(!$momoDTO->customer){
		// 	$momoDTO->customer = $this->billingClient->getAccountDetails($momoDTO->meterNumber);
		// }
		// $newBalance = (float)(\str_replace(",", "", $momoDTO->customer['balance'])) - 
		// 		(float)$momoDTO->receiptAmount;
		// $newBalance = \number_format($newBalance, 2, '.', ',');
		
		if(!$momoDTO->tokenNumber){
			$momoDTO->paymentStatus = "PAID | NO TOKEN";
			//receiptNumber = transactionid in Lukanga PrePaid Billing Client
			$momoDTO->receiptNumber =  now()->timestamp.\strtoupper(Str::random(6)); 
			$tokenParams = [
							"meterNumber"=> $momoDTO->meterNumber,
							"paymentAmount" => $momoDTO->receiptAmount,
							"transactionId" => $momoDTO->receiptNumber
						];
			$tokenResponse=$this->billingClient->generateToken($tokenParams);
			if($tokenResponse['status']=='SUCCESS'){
				$momoDTO->paymentStatus = "RECEIPTED";
				$momoDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$momoDTO->receipt = "Payment successful\n" .
											"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
											"Meter No: " . $momoDTO->meterNumber . "\n" .
											"Acc: " . $momoDTO->accountNumber . "\n".
											"Token: ". $momoDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$momoDTO->error = $tokenResponse['error'];
			}

		}


		return $momoDTO;

	}

}