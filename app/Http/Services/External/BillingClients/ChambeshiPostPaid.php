<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService;
use App\Http\Services\External\BillingClients\Chambeshi\Chambeshi;
use App\Http\Services\External\BillingClients\IBillingClient;
use Exception;

class ChambeshiPostPaid implements IBillingClient
{
    
   public function __construct(
         private ChambeshiAccountService $chambeshiAccountService,
         private Chambeshi $chambeshi,
         
      )
   {}
  
   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         $customer = $this->chambeshiAccountService->findOneBy(['AR_Acc'=>$params['customerAccount']]);
         if($customer){
            $district = $this->chambeshi->getDistrict(\trim($customer->AR_Acc));
            $response = [
                           "customerAccount" => \trim($customer->AR_Acc),
                           "name"=>\trim($customer->AR_Acc_Name),
                           "address"=>"",
                           "district" => $district,
                           "mobileNumber"=>"",
                           "balance"=> \number_format((float)$customer->AR_Acc_Bal, 2, '.', ',')
                        ];
         }else{
            throw new Exception("Invalid Chambeshi POST-PAID Account Number", 1);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw $e;
         }else{
            throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 2);
         }
      }

      return $response;
   }

   public function postPayment(array $postParams): array
   {
      return $this->chambeshi->postPayment($postParams);
   }

}