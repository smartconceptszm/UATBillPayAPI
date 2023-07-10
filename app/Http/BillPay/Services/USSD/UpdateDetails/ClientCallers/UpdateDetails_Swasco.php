<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\BillPay\Services\External\BillingClients\IBillingClient;

class UpdateDetails_Swasco implements IUpdateDetailsClient
{

   private $billingClient;
   public function __construct(IBillingClient $billingClient)
   {
      $this->billingClient = $billingClient;
   }

   public function create(array $detailsData): string
   {

      try{
         return $this->billingClient->changeCustomerDetail([
                     'accountNumber' => $detailsData['accountNumber'],
                     "phoneNumber" => $detailsData['mobileNumber'],
                     'newMobileNo' => $detailsData['details']
                  ]);
      } catch (\Throwable $e) {
         throw new Exception('At post updated customer details. '.$e->getMessage());
      }                                             

   }

}