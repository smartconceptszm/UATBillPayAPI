<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\EnquiryHandler;
use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPrePaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private EnquiryHandler $chambeshiEnquiry,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$newBalance = "0";
		if(!$paymentDTO->customer){
			$paymentDTO = $this->chambeshiEnquiry->handle($paymentDTO);
		}
		$newBalance = (float)(\str_replace(",", "", $paymentDTO->customer['balance'])) - 
										(float)$paymentDTO->receiptAmount;
		$newBalance = \number_format($newBalance, 2, '.', ',');

		if(!$paymentDTO->tokenNumber){
			$paymentDTO->paymentStatus = "PAID | NO TOKEN";
			$tokenParams = [
							"total_paid" => $paymentDTO->receiptAmount, 
							"meter_number"=> $paymentDTO->customerAccount,
							'client_id'=>$paymentDTO->client_id,
							"debt_percent"=> 50
						];
			$tokenResponse=$this->billingClient->generateToken($tokenParams);
			if($tokenResponse['status']=='SUCCESS'){
				$paymentDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$paymentDTO->paymentStatus = "PAID | NOT RECEIPTED";
			}else{
				$paymentDTO->error = $tokenResponse['error'];
			}

		}

		if($paymentDTO->tokenNumber && $paymentDTO->paymentStatus == "PAID | NOT RECEIPTED"){
			$paymentDTO->receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;
			$receiptingParams=[
					"TxDate"=> $paymentDTO->created_at,
					"Account"=> $paymentDTO->customerAccount,  
					"AccountName"=> $paymentDTO->customer['name'],
					"Debt"=> 0,
					"AmountPaid" => $paymentDTO->receiptAmount, 
					"Phone#"=> $paymentDTO->mobileNumber,
					"ReceiptNo"=> $paymentDTO->receiptNumber,
					"Address"=> $paymentDTO->customerAccount,
					"District"=> $paymentDTO->district,
					"TransactDescript"=> "2220 PREPAID. Token: ".$paymentDTO->tokenNumber,
					"Mobile Network" => $paymentDTO->walletHandler, 
			];

			$billingResponse=$this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status'] == 'SUCCESS'){
				$paymentDTO->paymentStatus = "RECEIPTED";
				$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->customerAccount . "\n".
											"Token: ". $paymentDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->receiptNumber = '';
				$paymentDTO->error = "At post payment. ".$billingResponse['error'];
			}
		}
		return $paymentDTO;

	}

}