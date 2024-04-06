<?php

namespace App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers;


use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\NkanaPrePaid;
use App\Http\DTOs\BaseDTO;

class NkanaPrePaidEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private NkanaPrePaid $billingClient)
	{}

	public function getAccountDetails(BaseDTO $txDTO):BaseDTO
	{

		try {
			$txDTO->customer = $this->billingClient->getAccountDetails([
															'meterNumber'=>$txDTO->meterNumber,
															'paymentAmount'=>$txDTO->paymentAmount
														]);
			$txDTO->accountNumber = $txDTO->customer['accountNumber'];
			$txDTO->district = $txDTO->customer['district'];
		} catch (\Throwable $e) {
			throw $e;
		}
		return $txDTO;
		
	}
    
}