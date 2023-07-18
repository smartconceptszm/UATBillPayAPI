<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\BillPay\Services\External\BillingClients\IBillingClient;

class Complaint_Swasco implements IComplaintClient
{
   
   private $billingClient;
   public function __construct(IBillingClient $billingClient)
   {
      $this->billingClient = $billingClient;
   }

   public function create(array $complaintData): string
   {

      try{
         return $this->billingClient->postComplaint([
                           'accountNumber' => $complaintData['accountNumber'],
                           'complaintCode' => $complaintData['code'],
                           "mobileNumber" => $complaintData['mobileNumber']
                     ]);
      } catch (\Throwable $e) {
         throw new Exception('At Post customer complaint. '.$e->getMessage());
      }                                             

   }

}