<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptServiceConnectionNkana implements IReceiptPayment
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$clientMenu = $this->clientMenuService->findById($paymentDTO->menu_id);

		$receiptingParams = [
									"clientRefnumber"=> $paymentDTO->ppTransactionId,
									"amount" => $paymentDTO->receiptAmount,
									"selectedId" => (string)$clientMenu->order,
									"client_id" => $paymentDTO->client_id
							];

		$billingResponse=$this->billingClient->postOtherPayment($receiptingParams);
		if($billingResponse['status'] == 'SUCCESS'){
			$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
			$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
			$paymentDTO->receipt = "\n"."Payment successful"."\n".
										"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
										"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
										"Acc: " . $paymentDTO->customerAccount . "\n";
										"Ref: " .\str_replace(\chr(47), "", $paymentDTO->reference). "\n".
										"Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}