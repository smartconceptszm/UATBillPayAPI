<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\External\BillingClients\IBillingClient;
use Exception;

class Complaint_Nkana implements IComplaintClient
{
   
   public function __construct(
      private IBillingClient $billingClient)
   {}

   public function create(array $complaintData): string
   {

      try{
         
         return $this->billingClient->postComplaint([
                              'complaintDescription' => $complaintData['complaintCode'].
                                                         " - ".$complaintData['complaintTypeName'].
                                                         " - ".$complaintData['complaintSubTypeName'],
                              'clientPhoneNumber' => $complaintData['mobileNumber'],
                              'custkey' => $complaintData['customerAccount'],
                              'postPaid' =>  $complaintData['postPaid'],
                              'client_id' => $complaintData['client_id']
                           ]);

      } catch (\Throwable $e) {
         throw new Exception('At Post customer complaint. '.$e->getMessage());
      }                                             

   }

}