<?php

namespace App\Http\Services\Utility;

use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class SCLExternalServiceBinder
{

	public function __construct(
		private ClientMenuService $clientMenuService
  	) {}

	public function bindAll(string $urlPrefix, string $menu_id, string $walletHandler): void
	{

		$this->bindBillingClient($urlPrefix,$menu_id);
		$this->bindReceiptingHandler($urlPrefix,$menu_id);
		$this->bindWallet($urlPrefix,$walletHandler);

	}

	public function bindBillingAndReceipting(string $urlPrefix, string $menu_id): void
	{

		$this->bindBillingClient($urlPrefix,$menu_id);
		$this->bindReceiptingHandler($urlPrefix,$menu_id);

	}

	public function bindBillingClient(string $urlPrefix, string $menu_id): void
	{

		$theMenu = $this->clientMenuService->findById($menu_id);
		$billpaySettings = $this->getBillpaySettings();
		
		$billingClient = $theMenu->billingClient;
		
		if ($this->shouldUseMockBilling($billpaySettings, $urlPrefix)) {
			 $billingClient = "MockBillingClient";
		}
		
		// Bind services to container for this job execution
		App::bind(
			 \App\Http\Services\External\BillingClients\IBillingClient::class, 
			 $billingClient
		);

	}

	public function bindReceiptingHandler(string $urlPrefix, string $menu_id): void
	{

		$theMenu = $this->clientMenuService->findById($menu_id);
		$billpaySettings = $this->getBillpaySettings();
		
		$receiptingHandler = $theMenu->receiptingHandler;
		
		// Override with mock services if configured
		if ($this->shouldUseMockReceipting($billpaySettings, $urlPrefix)) {
			 $receiptingHandler = "MockReceipting";
		}
		
		// Bind services to container 
		App::bind(
			 \App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment::class, 
			 $receiptingHandler 
		);
	}

	public function bindWallet(string $urlPrefix,string $walletHandler): void
	{

		$billpaySettings = $this->getBillpaySettings();
		if($this->shouldUseMockWallet($billpaySettings,$urlPrefix)){
			$walletHandler = 'MockWallet';
		}
		App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,$walletHandler);
		
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

	private function shouldUseMockWallet(array $settings, string $urlPrefix): bool
	{
		 $key = 'WALLET_USE_MOCK_' . strtoupper($urlPrefix);
		 return ($settings[$key] ?? '') === 'YES';
	}

}