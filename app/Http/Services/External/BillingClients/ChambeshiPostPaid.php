<?php

namespace App\Http\Services\External\BillingClients;

use \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService;
use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService;
use App\Http\Services\External\BillingClients\Chambeshi\Chambeshi;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Log;

use Exception;

class ChambeshiPostPaid extends Chambeshi implements IBillingClient
{
    
   public function __construct(
         protected ChambeshiPaymentService $chambeshiPaymentService,
         private ChambeshiAccountService $chambeshiAccountService
      )
   {
      parent::__construct(new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService(
                                 new \App\Models\ChambeshiPayment()
                              ));
   }
  
   public function getAccountDetails(string $accountNumber): array
   {

      $response = [];

      try {

         $customer = $this->chambeshiAccountService->findOneBy(['AR_Acc'=>$accountNumber]);
         $district = $this->getDistrict(\trim($customer->AR_Acc));

         $response = [
                        "accountNumber" => \trim($customer->AR_Acc),
                        "name"=>\trim($customer->AR_Acc_Name),
                        "address"=>"",
                        "district" => $district,
                        "mobileNumber"=>"",
                        "balance"=> \number_format((float)$customer->AR_Acc_Bal, 2, '.', ',')
                     ];
      
      } catch (\Throwable $e) {
         Log::error(' CHAMBESHI Billing Client - Get Balance '.$e->getMessage());
         if ($e->getCode() == 2) {
               throw new Exception($e->getMessage(), 2);
         } elseif ($e->getCode() == 1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 1);
         }
      }

      return $response;
   }

}