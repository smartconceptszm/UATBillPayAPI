<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptVacuumTankerSwasco implements IReceiptPayment
{

	public function __construct(
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$paymentDTO->customerAccount = '320008';
		$paymentDTO->customer['customerAccount'] = '320008';
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
									'account' => $paymentDTO->customerAccount,
									'amount' => $paymentDTO->receiptAmount,
									'client_id' => $paymentDTO->client_id,
									'paymentType'=>"12",
								];
		$billingResponse = $this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
			$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
			$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
			$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber  . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->customerAccount . "\n".
											"Ref: " .\str_replace(\chr(47), "", $paymentDTO->reference). "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
			$paymentDTO->error = "At post payment. ".$billingResponse['error'];
		}

		return $paymentDTO;

	}

}