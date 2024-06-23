<?php

namespace App\Http\Services\External\Adaptors\BillingEnquiryHandlers;


use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\LukangaPostPaid;
use App\Http\DTOs\BaseDTO;

class LukangaPostPaidEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private LukangaPostPaid $billingClient)
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