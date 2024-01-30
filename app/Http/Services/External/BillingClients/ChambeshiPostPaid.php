<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService;
use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService;

use Exception;

class ChambeshiPostPaid implements IBillingClient
{
    
   private $districts =[
      "KS"=>"KASAMA",
      "CHL"=>"CHINSALI",
   ];

   public function __construct(
      private ChambeshiAccountService $chambeshiAccountService,
      private ChambeshiPaymentService $chambeshiPaymentService,
     
      )
  {}
  
   public function getAccountDetails(string $accountNumber): array
   {

      $response = [];

      try {

         //Populate the Response Array
         $customer = $this->chambeshiAccountService->findOneBy(['AR_Acc'=>$accountNumber]);
         // $district = $this->getDistrict(\strtoupper(\substr($response['accountNumber'],0,3)));
         //$customer = json_decode($customer);

         $response = [
                           "accountNumber" => \trim($customer->AR_Acc),
                           "name"=>\trim($customer->AR_Acc_Name),
                           "address"=>"",
                           "district" => 'OTHER',
                           "mobileNumber"=>"",
                           "balance"=> $customer->AR_Acc_Bal,
                     ];

      
      } catch (\Throwable $e) {
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

   public function postPayment(Array $postParams): Array 
   {

      $response=[
         'status'=>'FAILED',
         'receiptNumber'=>'',
         'error'=>''
      ];

      try {
         $payment = $this->chambeshiPaymentService->create($postParams);
         if($payment){
            $response['status'] = "SUCCESS";
            $response['receiptNumber'] = $payment->ReceiptNo;
         }else{
            $response['error'] = "Unable to create payment Record";
         }
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

      return $response;
     
   }


   public function getDistrict(String $code): string
   {
      
      if(\array_key_exists($code,$this->districts)){
         return $this->districts[$code];
      }else{
         return "OTHER";
      }
      
   }


}