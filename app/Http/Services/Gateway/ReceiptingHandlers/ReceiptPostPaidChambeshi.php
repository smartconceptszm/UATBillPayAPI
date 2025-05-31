<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\PostLocalReceipt;
use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private PostLocalReceipt $postLocalReceipt,
		private EnquiryHandler $chambeshiEnquiry,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
		$newBalance = "0";
		if(!$paymentDTO->customer && $theMenu->paymentType == 'PostPaid'){
			$paymentDTO = $this->chambeshiEnquiry->handle($paymentDTO);
			$newBalance = (float)(\str_replace(",", "", $paymentDTO->customer['balance'])) - 
						(float)$paymentDTO->receiptAmount;
			$newBalance = \number_format($newBalance, 2, '.', ',');
		}

		$receiptingParams = $this->postLocalReceipt->handle($paymentDTO,$theMenu);
		$billingResponse = $this->billingClient->postPayment($receiptingParams);
		$paymentDTO->receiptNumber =  $receiptingParams['ReceiptNo'];
		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipted->value;
			$paymentDTO->receipt = "\n"."Payment successful"."\n".
										"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
										"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n";
			if($theMenu->onOneAccount == 'YES'){
				$paymentDTO->receipt .= "Service: ".$theMenu->prompt."\n";
				if($theMenu->requiresReference == 'YES'){
					$customerJourney = explode("*", $paymentDTO->customerJourney);
					$paymentDTO->receipt .= "Ref: ".$customerJourney[4]."\n";
				}
			}else{
				$paymentDTO->receipt .= "Acc: ".$paymentDTO->customerAccount."\n";
			}
			$paymentDTO->receipt.="Date: ".Carbon::now()->format('d-M-Y') . "\n";
			
		}else{
			$paymentDTO->receiptNumber =  '';
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}