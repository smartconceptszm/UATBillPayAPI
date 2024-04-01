<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
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

		$momoDTO->accountNumber = '320008';
		$momoDTO->customer['accountNumber'] = '320008';
		$momoDTO->customer['name'] = 'Vacuum Tanker Pit Emptying';
		//Trimmed to 20 cause of constraint on API
		$referenceNumber ='';
		if($momoDTO->reference){
			$referenceNumber = \strlen($momoDTO->reference) > 20 ? 
													\substr($momoDTO->reference, 0, 20) : $momoDTO->reference;
		}else{
			$arrCustomerJourney = \explode("*", $momoDTO->customerJourney);		
			$referenceNumber = \strlen($arrCustomerJourney[3]) > 20 ? 
													\substr($arrCustomerJourney[3], 0, 20) : $arrCustomerJourney[3];
		}

												
		$receiptingParams = [ 
										'paymentType'=>'12',
										'account' => $momoDTO->accountNumber,
										'amount' => $momoDTO->receiptAmount,
										'mobileNumber'=> $momoDTO->mobileNumber,
										'referenceNumber' => $referenceNumber,
								];

		$billingResponse = $this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
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