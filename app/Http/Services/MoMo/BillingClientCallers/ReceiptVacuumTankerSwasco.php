<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class ReceiptVacuumTankerSwasco implements IReceiptPayment
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		//Trimmed to 20 cause of constraint on API
		$arrCustomerJourney = \explode("*", $momoDTO->customerJourney);
		$momoDTO->reference = \strlen($arrCustomerJourney[3]) > 20 ? 
												\substr($arrCustomerJourney[3], 0, 20) : $arrCustomerJourney[3];
												
		$receiptingParams=[ 
				'paymentType'=>'12',
				'account' => $momoDTO->accountNumber,
				'amount' => $momoDTO->receiptAmount,
				'mobileNumber'=> $momoDTO->mobileNumber,
				'referenceNumber' => $momoDTO->reference,
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status']=='SUCCESS'){
				$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
				$momoDTO->paymentStatus = "RECEIPTED";
				$momoDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $momoDTO->receiptNumber  . "\n" .
											"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $momoDTO->accountNumber . "\n".
											"Ref: " .\str_replace(\chr(47), "", $momoDTO->reference). "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}

		return $momoDTO;

	}

}