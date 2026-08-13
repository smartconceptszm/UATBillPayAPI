<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\DTOs\BaseDTO;

class ReceiptOtherPaymentsMulonga implements IReceiptPayment
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		//Trimmed to 20 cause of constraint on API
		$clientMenu = $this->clientMenuService->findById($paymentDTO->menu_id);

		$receiptingParams = [ 
										'receiptTypeCode' => $clientMenu->cAccountCode,
										'applicationNumber' => Str::substr($paymentDTO->reference,0,20),
										'referenceNumber' => Str::substr($paymentDTO->reference,0,20),
										'receiptType' => $clientMenu->cAccountCode,
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'amount' => $paymentDTO->receiptAmount,
										'description' => $clientMenu->prompt,
										'client_id' => $paymentDTO->client_id,
										'paymentType'=>"999",
									];

				$billingResponse=$this->billingClient->postOtherPayment($receiptingParams);

		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
			$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;

			$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"For: " . $clientMenu->prompt. "\n";
											"Ref: " . $paymentDTO->reference. "\n";
			$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
			
		}else{
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;


	}

}