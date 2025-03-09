<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Payments\ReceiptService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private EnquiryHandler $chambeshiEnquiry,
		private ReceiptService $receiptService,
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


		$receipt = $this->receiptService->findOneBy([
													'client_id'=>$paymentDTO->client_id,
													'payment_id'=>$paymentDTO->id
												]);
		if(!$receipt){
					$receipt = $this->receiptService->create([
								'description' => $paymentDTO->receipt,
								'client_id'=>$paymentDTO->client_id,
								'payment_id'=>$paymentDTO->id
							]);
		}

		$paymentDTO->receiptNumber = $receipt->id;

		$receiptingParams = [
									"payment_provider" => strtolower($paymentDTO->walletHandler).'_money', 
									"payer_msisdn"=> $paymentDTO->mobileNumber, 
									"txnDate"=> Carbon::now()->format('Y-m-d'),
									"account"=> $paymentDTO->customerAccount,
									"amount" => $paymentDTO->receiptAmount,
									"txnId"=> $paymentDTO->transactionId,
									"client_id"=> $paymentDTO->client_id,
									"ReceiptNo"=> $paymentDTO->receiptNumber,
									"transDesc"=>"PostPaid"
								];

		$billingResponse = $this->billingClient->postPayment($receiptingParams);
	
		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipted->value;
			$paymentDTO->receipt = "\n"."Payment successful"."\n".
										"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
										"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
										"Acc: " . $paymentDTO->customerAccount . "\n";
			$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
			
		}else{
			$paymentDTO->receiptNumber =  '';
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}