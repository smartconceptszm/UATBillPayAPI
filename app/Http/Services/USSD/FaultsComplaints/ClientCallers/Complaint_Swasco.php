<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\External\BillingClients\IBillingClient;
use Exception;

class Complaint_Swasco implements IComplaintClient
{
   
   public function __construct(
      private IBillingClient $billingClient)
   {}

   public function create(array $complaintData): string
   {

      try{
         return $this->billingClient->postComplaint([
                              'accountNumber' => $complaintData['accountNumber'],
                              'complaintCode' => $complaintData['complaintCode'],
                              'mobileNumber' => $complaintData['mobileNumber']
                           ]);
      } catch (Exception $e) {
         throw new Exception('At Post customer complaint. '.$e->getMessage());
      }                                             

   }

}