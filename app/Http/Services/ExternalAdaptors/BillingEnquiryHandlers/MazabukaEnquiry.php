<?php

namespace App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers;


use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\DTOs\BaseDTO;

class MazabukaEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private IBillingClient $billingClient)
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