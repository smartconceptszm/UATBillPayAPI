<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidChambeshi implements IReceiptPayment
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

		$paymentDTO->receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;

		$receiptingParams = [
									"Mobile Network" => $paymentDTO->walletHandler, 
									"AccountName"=> $paymentDTO->customer['name'],
									"AmountPaid" => $paymentDTO->receiptAmount,
									"ReceiptNo"=> $paymentDTO->receiptNumber,
									"Address"=> $paymentDTO->customerAccount,
									"Account"=> $paymentDTO->customerAccount,
									"TransactDescript"=> "2220 POST-PAID.",
									"Phone#"=> $paymentDTO->mobileNumber,
									"District"=> $paymentDTO->revenuePoint,
									"TxDate"=> $paymentDTO->created_at,
									"Debt"=> 0,
							];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
	
		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipted->value;
			$paymentDTO->receipt = "\n"."Payment successful"."\n".
										"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
										"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
										"Acc: " . $paymentDTO->customerAccount . "\n";
			$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
			$paymentDTO->receipt.="Balance update within 48 hrs.";
		}else{
			$paymentDTO->receiptNumber =  '';
			$paymentDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}