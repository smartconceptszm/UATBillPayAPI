<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;

class BillingMock implements IBillingClient
{
    
   public function __construct()
   {}

   public function getAccountDetails(array $params): array
   {

      return [
               "customerAccount" => $params['customerAccount'],
               "name" => "Mock Customer",
               "address" => "No. 1, Street 1",
               "revenuePoint" => "MOCK",
               "consumerType" => "DOMESTIC",
               "consumerTier" => "DOMESTIC",
               "mobileNumber" => "260761028631",
               "balance" => \number_format(100, 2, '.', ','),
            ]; 
   }

   public function postPayment(Array $postParams): Array 
   {

      $response=[
            'status'=>'SUCCESS',
            'receiptNumber'=>"RCPT".\rand(1000,100000),
            'error'=>''
         ];

      return $response;
   }

   public function generateToken(Array $postParams): Array
   {

      $response=[
         'status'=>'SUCCESS',
         'tokenNumber'=> \implode('-', \str_split(str_replace(' ', '', "TOKN".\rand(100000000000,999999999999)), 4)),
         'error'=>''
      ];
      return $response;

   }

   public function postComplaint(array $postParams): String {

      $response="COMPLAINT:".\rand(1000,100000);
      return $response;

   }



}
