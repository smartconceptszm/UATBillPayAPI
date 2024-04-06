<?php

namespace App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers;


use App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\AbEnquiryHandler;
use App\Http\Services\External\BillingClients\ChambeshiPrePaid;
use App\Http\DTOs\BaseDTO;

class ChambeshiPrePaidEnquiry extends AbEnquiryHandler
{

	public function __construct(
		private ChambeshiPrePaid $billingClient)
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