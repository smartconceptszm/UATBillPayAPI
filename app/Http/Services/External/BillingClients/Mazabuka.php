<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;

use Exception;

class Mazabuka implements IBillingClient
{
    
   public function __construct()
   {}

   public function getAccountDetails(string $accountNumber): array
   {

      $response = [
         'accountNumber' => $accountNumber,
         "name" => "Mazabuka Customer",
         "address" => "No. 1, Street 1",
         "district" => 'MAZABUKA',
         "mobileNumber" => "",
         "balance" => \number_format(100, 2, '.', ','),
      ];
      return $response;
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

}
