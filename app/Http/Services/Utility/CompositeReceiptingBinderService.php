<?php

namespace App\Http\Services\Utility;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentTypeEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class CompositeReceiptingBinderService
{

	public function __construct(
		private ClientMenuService $clientMenuService
  ) {}

	public function bind(string $urlPrefix, string $menu_id): void
	{

		$theMenu = $this->clientMenuService->findById($menu_id);
		$billpaySettings = $this->getBillpaySettings();
		
		if($theMenu->paymentType == PaymentTypeEnum::PrePaid->value){
			$receiptingHandler = 'ReceiptCompositePrePaid'.strtoupper($urlPrefix);
		}else{
			$receiptingHandler = 'ReceiptComposite'.strtoupper($urlPrefix);
		}
		$billingClient = $theMenu->billingClient;
		
		// Override with mock services if configured
		if ($this->shouldUseMockReceipting($billpaySettings, $urlPrefix)) {
			 $receiptingHandler = "MockReceipting";
		}
		
		if ($this->shouldUseMockBilling($billpaySettings, $urlPrefix)) {
			 $billingClient = "MockBillingClient";
		}
		
		// Bind services to container for this job execution
		App::bind(
			 \App\Http\Services\External\BillingClients\IBillingClient::class, 
			 $billingClient
		);
		App::bind(
			 \App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class, 
			 $receiptingHandler 
		);
	}

	private function getBillpaySettings(): array
	{
		 return json_decode(
			  Cache::get('billpaySettings', '{}'), 
			  true
		 ) ?: [];
	}

	private function shouldUseMockReceipting(array $settings, string $urlPrefix): bool
	{
		 $key = 'USE_RECEIPTING_MOCK_' . strtoupper($urlPrefix);
		 return ($settings[$key] ?? '') === 'YES';
	}

	private function shouldUseMockBilling(array $settings, string $urlPrefix): bool
	{
		 $key = 'USE_BILLING_MOCK_' . strtoupper($urlPrefix);
		 return ($settings[$key] ?? '') === 'YES';
	}

}