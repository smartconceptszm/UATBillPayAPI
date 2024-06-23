<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\Swasco;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptVacuumTankerSwasco implements IReceiptPayment
{

	public function __construct(
		private Swasco $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$paymentDTO->accountNumber = '320008';
		$paymentDTO->customer['accountNumber'] = '320008';
		$paymentDTO->customer['name'] = 'Vacuum Tanker Pit Emptying';
		//Trimmed to 20 cause of constraint on API
		$referenceNumber ='';
		if($paymentDTO->reference){
			$referenceNumber = \strlen($paymentDTO->reference) > 20 ? 
													\substr($paymentDTO->reference, 0, 20) : $paymentDTO->reference;
		}else{
			$arrCustomerJourney = \explode("*", $paymentDTO->customerJourney);		
			$referenceNumber = \strlen($arrCustomerJourney[3]) > 20 ? 
													\substr($arrCustomerJourney[3], 0, 20) : $arrCustomerJourney[3];
		}

		$receiptingParams = [ 
									'mobileNumber'=> $paymentDTO->mobileNumber,
									'referenceNumber' => $referenceNumber,
									'account' => $paymentDTO->accountNumber,
									'amount' => $paymentDTO->receiptAmount,
									'client_id' => $paymentDTO->client_id,
									'paymentType'=>"12",
								];
		$billingResponse = $this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
				$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = "RECEIPTED";
				$paymentDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $paymentDTO->receiptNumber  . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->accountNumber . "\n".
											"Ref: " .\str_replace(\chr(47), "", $paymentDTO->reference). "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$paymentDTO->error = "At post payment. ".$billingResponse['error'];
		}

		return $paymentDTO;

	}

}