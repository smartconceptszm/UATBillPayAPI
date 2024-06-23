<?php

namespace App\Http\Services\External\Adaptors\BillingEnquiryHandlers;


use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\KafubuPostPaid;
use App\Http\DTOs\BaseDTO;

class KafubuPostPaidEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private KafubuPostPaid $billingClient)
	{}

	public function getAccountDetails(BaseDTO $txDTO):BaseDTO
	{

		try {
			$txDTO->customer = $this->billingClient->getAccountDetails([
																			'accountNumber'=>$txDTO->accountNumber,
																			'client_id'=>$txDTO->client_id
																		]);
			$txDTO->district = $txDTO->customer['district'];
		} catch (\Throwable $e) {
			throw $e;
		}
		return $txDTO;
		
	}
    
}