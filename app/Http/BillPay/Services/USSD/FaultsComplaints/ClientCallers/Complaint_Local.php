<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\BillPay\Services\ComplaintService;
use Exception;

class Complaint_Local implements IComplaintClient
{
    private $complaintService;
    public function __construct(ComplaintService $complaintService)
    {
       $this->complaintService = $complaintService;
    }

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