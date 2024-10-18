<?php

use App\Http\Services\External\PaymentsProviderClients\MTNMoMo;
use Tests\TestCase;

class MTNMoMoTest extends TestCase
{

   public function _testRequestPayment()
   {   

      $momoParams=[];
      $momoParams['wallet_id']="d617b452-7307-11ee-b8ce-fec6e52a2330";
      $momoParams['transactionId']="";
      $momoParams['customerAccount']="1101000186";
      $momoParams['paymentAmount']='1.10';
      $momoParams['walletNumber']= '260965199175';
      $mtnClient=new MTNMoMo(new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential([]),));
      $response = $mtnClient->requestPayment((object)$momoParams);
      $this->assertTrue($response['status']=="SUCCESS");

   }

   public function _testConfirmPayment()
   {   

      $momoParams=[];
      $momoParams['wallet_id']="d617b452-7307-11ee-b8ce-fec6e52a2330";
      $momoParams['transactionId']="eccab365-3b44-4b51-951c-6a92d2b9ec2a";
      $mtnClient=new MTNMoMo(new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()));
      $response = $mtnClient->confirmPayment((object)$momoParams);
      $this->assertTrue($response['status']=="SUCCESS");

   }

   public function _testDeliverNotification()
   {   

      $momoParams=[];
      $momoParams['transactionId']="";
      $momoParams['wallet_id']="d617b452-7307-11ee-b8ce-fec6e52a2330";
      $momoParams['message'] = "Testing notification delivery for an MTN Transaction. Number : ".
                                                               $momoParams['transactionId'];
      $mtnClient=new MTNMoMo(new \App\Http\Services\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()));
      $response = $mtnClient->deliverNotification($momoParams);
      $this->assertTrue($response['status']=="SUCCESS");

   }

}