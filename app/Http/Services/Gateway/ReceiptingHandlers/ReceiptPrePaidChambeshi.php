<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\PostThePrePaidToBillingJob;
use Illuminate\Support\Facades\Cache;
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
				$paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
				$paymentDTO->tokenNumber = $tokenResponse['tokenNumber'];
				$paymentDTO->receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;
				$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc/Meter No: " . $paymentDTO->customerAccount . "\n" .
											"Token: ". $paymentDTO->tokenNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
				//Post the Payment to the Billing System
				$billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
				PostThePrePaidToBillingJob::dispatch($paymentDTO)
                                 ->delay(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY']))
                                 ->onQueue('low');

			}else{
				$paymentDTO->error = $tokenResponse['error'];
			}
		}else if($paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value){

			if(!$paymentDTO->receiptNumber){
				$paymentDTO->receiptNumber =  $paymentDTO->customerAccount."_".\now()->timestamp;
			}

			$receiptingParams = [
										"payment_provider" => strtolower($paymentDTO->walletHandler).'_money', 
										"payer_msisdn"=> $paymentDTO->mobileNumber, 
										"txnDate"=> Carbon::now()->format('Y-m-d'),
										"ReceiptNo"=> $paymentDTO->receiptNumber,
										"account"=> $paymentDTO->customerAccount,
										"amount" => $paymentDTO->receiptAmount,
										"txnId"=> $paymentDTO->transactionId,
										"client_id"=> $paymentDTO->client_id,
										"transDesc"=>"PrePaid"
									];

			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
					$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipt_Delivered->value;
			}else{
				$paymentDTO->receiptNumber = '';
				$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
			}

		}

		return $paymentDTO;

	}



}