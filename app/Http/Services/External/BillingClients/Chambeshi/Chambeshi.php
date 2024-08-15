<?php

namespace App\Http\Services\External\BillingClients\Chambeshi;

use \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService;

class Chambeshi
{

   private $districts =[
      "CHL"=>"Chilubi",
      "CHN"=>"Chinsali",
      "ISO"=>"Isoka",
      "KAP"=>"Kaputa",
      "KCT"=>"Kasama Central Town",
      "KMH"=>"Kasama Mulenga Hills",
      "LUW"=>"Luwingu",
      "MBA"=>"Mbala",
      "MPI"=>"Mpika", 
      "MPU"=>"Mpika", 
      "MPO"=>"Mporokoso", 
      "MUN"=>"Mpulungu", 
      "NAK"=>"Nakonde"
   ];

   public function __construct(
      private ChambeshiPaymentService $chambeshiPaymentService,
   )
   {}
  
   public function postPayment(Array $postParams): Array 
   {

      $response = [
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
         $response['error'] = "Unable to create payment Record: " .$e->getMessage();
      }
      return $response;
     
   }

   public function getDistrict(String $customerAccount): string
   {

      try {
         $arrAccountCharacter = \str_split($customerAccount);
         $strCode = "";
         foreach ($arrAccountCharacter as $value) {
            if(\is_numeric($value)){
               break;
            }else{
               $strCode .= \strtoupper($value); 
            }
         }
         if(\array_key_exists($strCode,$this->districts)){
            return $this->districts[$strCode];
         }else{
            return "OTHER";
         }
      } catch (\Throwable $th) {
         return "OTHER";
      }

      
   }


}