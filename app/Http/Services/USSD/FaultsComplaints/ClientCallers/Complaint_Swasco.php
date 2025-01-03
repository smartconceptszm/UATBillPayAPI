<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\External\BillingClients\Swasco;
use Exception;

class Complaint_Swasco implements IComplaintClient
{
   
   public function __construct(
      private Swasco $billingClient)
   {}

   public function create(array $complaintData): string
   {

      try{
         return $this->billingClient->postComplaint([
                              'customerAccount' => $complaintData['customerAccount'],
                              'complaintCode' => $complaintData['complaintCode'],
                              'mobileNumber' => $complaintData['mobileNumber'],
                              'created_at' => $complaintData['created_at'],
                              'client_id' => $complaintData['client_id']
                           ]);
      } catch (\Throwable $e) {
         throw new Exception('At Post customer complaint. '.$e->getMessage());
      }                                             

   }

}