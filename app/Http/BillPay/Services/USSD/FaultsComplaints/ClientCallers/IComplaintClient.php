<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers;

interface IComplaintClient 
{
   public function create(array $complaintData): string;
}