<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;

use Exception;

class Mazabuka implements IBillingClient
{
    
   public function __construct()
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [
         'customerAccount' => $params['customerAccount'],
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

      $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $firstChar = $alphabet[\random_int(0, strlen($alphabet) - 1)];
      $digits = \str_pad(\random_int(0, 99999), 5, '0', STR_PAD_LEFT);
      $response=[
            'status'=>'SUCCESS',
            'receiptNumber'=>"MAZ".\date('ymd').".".\date('His').".".$firstChar.$digits,
            'error'=>''
         ];
      return $response;
      
   }

}
