<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

interface IComplaintClient 
{
   public function create(array $complaintData): string;
}