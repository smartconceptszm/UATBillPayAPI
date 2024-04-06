<?php

namespace App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers;


use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\Swasco;
use App\Http\DTOs\BaseDTO;

class SwascoEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private Swasco $billingClient)
	{}

	public function getAccountDetails(BaseDTO $txDTO):BaseDTO
	{

		try {
			$txDTO->customer = $this->billingClient->getAccountDetails(['accountNumber'=>$txDTO->accountNumber]);
			$txDTO->district = $txDTO->customer['district'];
		} catch (\Throwable $e) {
			throw $e;
		}
		return $txDTO;
		
	}
    
}