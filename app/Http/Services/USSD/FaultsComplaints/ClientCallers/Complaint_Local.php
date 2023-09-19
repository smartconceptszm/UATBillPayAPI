<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\CRM\ComplaintService;
use Exception;

class Complaint_Local implements IComplaintClient
{

	public function __construct(
		private ComplaintService $complaintService)
	{}

	public function create(array $complaintData):string
	{
		try{
			unset($complaintData['complaintCode']);
			$complaint = $this->complaintService->create($complaintData);
			return $complaint->caseNumber;
					
		} catch (\Throwable $e) {
			throw new Exception('At Post customer complaint. '.$e->getMessage());
		}                                             

	}

}