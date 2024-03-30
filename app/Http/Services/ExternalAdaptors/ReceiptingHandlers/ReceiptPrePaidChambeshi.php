<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;


class ReceiptPrePaidChambeshi implements IReceiptPayment
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
			$tokenParams = [
							"meter_number"=> $momoDTO->meterNumber,
							"total_paid" => $momoDTO->receiptAmount, 
							"debt_percent"=> 50
						];
			$tokenResponse=$this->billingClient->generateToken($tokenParams);
			if($tokenResponse['status']=='SUCCESS'){
				$momoDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$momoDTO->paymentStatus = "PAID | NOT RECEIPTED";
			}else{
				$momoDTO->error = $tokenResponse['error'];
			}

		}

		if($momoDTO->tokenNumber && $momoDTO->paymentStatus == "PAID | NOT RECEIPTED"){
			$momoDTO->receiptNumber =  $momoDTO->accountNumber."_".date('YmdHis');
			$receiptingParams=[
					"TxDate"=> $momoDTO->created_at,
					"Account"=> $momoDTO->accountNumber,  
					"AccountName"=> $momoDTO->customer['name'],
					"Debt"=> 0,
					"AmountPaid" => $momoDTO->receiptAmount, 
					"Phone#"=> $momoDTO->mobileNumber,
					"ReceiptNo"=> $momoDTO->receiptNumber,
					"Address"=> $momoDTO->accountNumber,
					"District"=> $momoDTO->district,
					"TransactDescript"=> "2220 PREPAID. Token: ".$momoDTO->tokenNumber,
					"Mobile Network" => $momoDTO->mnoName, 
			];

			$billingResponse=$this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status'] == 'SUCCESS'){
				$momoDTO->paymentStatus = "RECEIPTED";

				$momoDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $momoDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $momoDTO->accountNumber . "\n".
											"Token: ". $momoDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$momoDTO->error = "At post payment. ".$billingResponse['error'];
			}
		}
		return $momoDTO;

	}

}