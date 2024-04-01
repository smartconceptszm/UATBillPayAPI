<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\ChambeshiPostPaidEnquiry;
use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;


class ReceiptPostPaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private ChambeshiPostPaidEnquiry $chambeshiEnquiry,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		$newBalance = "0";
		if(!$momoDTO->customer){
			$momoDTO = $this->chambeshiEnquiry->handle($momoDTO);
		}
		$newBalance = (float)(\str_replace(",", "", $momoDTO->customer['balance'])) - 
										(float)$momoDTO->receiptAmount;
		$newBalance = \number_format($newBalance, 2, '.', ',');

		$momoDTO->receiptNumber =  $momoDTO->accountNumber."_".\now()->timestamp;

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
            "TransactDescript"=> "2220 POSTPAID.",
            "Mobile Network" => $momoDTO->mnoName, 
       ];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
	
		if($billingResponse['status']=='SUCCESS'){
			$momoDTO->paymentStatus = "RECEIPTED";
			$momoDTO->receipt = "Payment successful\n" .
										"Rcpt No: " . $momoDTO->receiptNumber . "\n" .
										"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
										"Acc: " . $momoDTO->accountNumber . "\n";
			$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
			$momoDTO->receipt.="Balance update within 48 hrs.";
		}else{
			$momoDTO->receiptNumber =  '';
			$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $momoDTO;

	}

}