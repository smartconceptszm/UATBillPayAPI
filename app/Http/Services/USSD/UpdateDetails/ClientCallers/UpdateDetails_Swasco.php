<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\Services\External\BillingClients\IBillingClient;
use Exception;

class UpdateDetails_Swasco implements IUpdateDetailsClient
{

   public function __construct(
      private IBillingClient $billingClient)
   {}

   public function create(array $detailsData): string
   {

      try{

         return $this->billingClient->changeCustomerDetail([
                     'accountNumber' => $detailsData['accountNumber'],
                     "phoneNumber" => $detailsData['mobileNumber'],
                     'newMobileNo' => $detailsData['updates'][1]
                  ]);
      } catch (\Throwable $e) {
         throw new Exception('At post updated customer details. '.$e->getMessage());
      }                                             

   }

}