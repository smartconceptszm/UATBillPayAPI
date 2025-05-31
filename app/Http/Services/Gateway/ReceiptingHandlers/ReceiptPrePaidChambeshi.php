<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\PostLocalReceipt;
use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\PostThePrePaidToBillingJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPrePaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private PostLocalReceipt $postLocalReceipt,
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

			$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
			$receiptingParams = $this->postLocalReceipt->handle($paymentDTO,$theMenu);
			$billingResponse = $this->billingClient->postPayment($receiptingParams);
			$paymentDTO->receiptNumber =  $receiptingParams['ReceiptNo'];
			if($billingResponse['status']=='SUCCESS'){
				$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipted->value;
			}else{
				$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
			}

		}

		return $paymentDTO;

	}



}