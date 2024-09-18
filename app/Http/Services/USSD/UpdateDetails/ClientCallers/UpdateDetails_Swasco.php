<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\Services\External\BillingClients\Swasco;
use Exception;

class UpdateDetails_Swasco implements IUpdateDetailsClient
{

   public function __construct(
      private Swasco $billingClient)
   {}

   public function create(array $detailsData): string
   {

      try{
         $newMobileNumber = \substr($detailsData['updates'][1],2);
         return $this->billingClient->changeCustomerDetail([
                                    'customerAccount' => $detailsData['customerAccount'],
                                    "mobileNumber" => $detailsData['mobileNumber'],
                                    'newMobileNumber' => $newMobileNumber,
                                    'client_id' => $detailsData['client_id']
                                 ]);
      } catch (\Throwable $e) {
         throw new Exception('At post updated customer details. '.$e->getMessage());
      }                                             

   }

}