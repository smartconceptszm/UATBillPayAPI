<?php

namespace App\Http\Services\External\Adaptors\BillingEnquiryHandlers;


use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\NkanaPostPaid;
use App\Http\DTOs\BaseDTO;

class NkanaPostPaidEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private NkanaPostPaid $billingClient)
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