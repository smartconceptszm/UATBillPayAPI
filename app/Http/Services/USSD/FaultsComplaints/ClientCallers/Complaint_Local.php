<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\Web\CRM\ComplaintService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use Exception;

class Complaint_Local implements IComplaintClient
{

	public function __construct(
		private ComplaintService $complaintService)
	{}

	public function create(array $complaintData):string
	{
		try{
			$urlPrefix = $complaintData['urlPrefix'];
			unset($complaintData['complaintCode']);
			unset($complaintData['urlPrefix']);
			$complaint = $this->complaintService->create($complaintData);

			$arrSMSes = [
					[
						'customerAccount' => $complaintData['customerAccount'],
						'mobileNumber' => $complaintData['mobileNumber'],
						'client_id' => $complaintData['client_id'],
						'urlPrefix' => $urlPrefix,
						'message' => "Complaint(Fault) successfully submitted. Case number: ".$complaint->caseNumber,
						'type' => 'NOTIFICATION',
					]
				];
			Queue::later(Carbon::now()->addSeconds(3), 
								new SendSMSesJob($arrSMSes,$urlPrefix),'','low');

			return $complaint->caseNumber;
					
		} catch (\Throwable $e) {
			throw new Exception('At Post customer complaint. '.$e->getMessage());
		}                                             

	}

}