<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Payments\ReceiptService;

use Exception;

class MazabukaLocal implements IBillingClient
{
    
   public function __construct(
      private ReceiptService $receiptService
   )
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [
                     'customerAccount' => $params['customerAccount'],
                     "name" => "Mazabuka Customer",
                     "composite" => "ORDINARY",
                     "address" => "MAZABUKA",
                     "revenuePoint" => 'MAZABUKA',
                     "consumerTier" => '',
                     "consumerType" => '',
                     "mobileNumber" => "",
                     "balance" => \number_format(0, 2, '.', ','),
                  ];
      return $response;
   }

   public function postPayment(Array $postParams): Array 
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];
      try {
         $receipt = $this->receiptService->create([
                                          'payment_id'=>$postParams['payment_id'],
                                          'client_id'=>$postParams['client_id']
                                       ]);
         $response['receiptNumber'] = $receipt->id;
         $response['status'] = 'SUCCESS';
      } catch (\Throwable $th) {
         $response['error'] = $th->getMessage();
      }
      return $response;
      
   }

}
