<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Gateway\ReceiptingHandlers\PostLocalReceipt;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class PostTokenToBilling
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private PostLocalReceipt $postLocalReceipt,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{


		$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);

		$receiptingParams = $this->postLocalReceipt->handle($paymentDTO,$theMenu);

		$billingResponse = $this->billingClient->postPayment($receiptingParams);

		$paymentDTO->receiptNumber =  $receiptingParams['ReceiptNo'];
		
		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->paymentStatus =  PaymentStatusEnum::Receipted->value;
		}else{
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}

		return $paymentDTO;

	}



}