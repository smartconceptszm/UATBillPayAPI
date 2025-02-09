<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
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

		if( $paymentDTO->tokenNumber == '' && $paymentDTO->paymentStatus == PaymentStatusEnum::NoToken->value){				
			$tokenParams = [
										"total_paid" => $paymentDTO->receiptAmount, 
										"meter_number"=> $paymentDTO->customerAccount,
										'client_id'=>$paymentDTO->client_id,
										"debt_percent"=> 50
									];
			$tokenResponse=$this->billingClient->generateToken($tokenParams);
			if($tokenResponse['status']=='SUCCESS'){
				$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
				$paymentDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$paymentDTO->receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;
				$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc/Meter No: " . $paymentDTO->customerAccount . "\n" .
											"Token: ". $paymentDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->error = $tokenResponse['error'];
			}
		}

		// if(($paymentDTO->tokenNumber != '') && ($paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value)){
		// 	$receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;
		// 	$receiptingParams = [
		// 									"payment_provider"=> strtolower($paymentDTO->walletHandler).'_money',
		// 									"payer_msisdn"=> $paymentDTO->mobileNumber, 
		// 									"txnDate"=> Carbon::now()->format('Y-m-d'),
		// 									"account"=> $paymentDTO->customerAccount,  
		// 									"amount" => $paymentDTO->receiptAmount, 
		// 									"txnId" => $paymentDTO->transactionId,
		// 									"client_id"=> $paymentDTO->client_id,
		// 									"ReceiptNo"=> $receiptNumber
		// 								];
		// 	$billingResponse = $this->billingClient->postPayment($receiptingParams);
		// 	if($billingResponse['status'] == 'SUCCESS'){
		// 		$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
		// 		$paymentDTO->receiptNumber =  $receiptNumber;
		// 		$paymentDTO->receipt = "\n"."Payment successful"."\n".
		// 									"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
		// 									"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
		// 									"Acc: " . $paymentDTO->customerAccount . "\n".
		// 									"Token: ". $paymentDTO->tokenNumber . "\n".
		// 									"Date: " . Carbon::now()->format('d-M-Y') . "\n";
		// 	}else{
		// 		$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		// 		$paymentDTO->receiptNumber =  '';
		// 	}
		//}
		return $paymentDTO;

	}

}