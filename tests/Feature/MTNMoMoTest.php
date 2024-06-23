<?php

use App\Http\Services\External\PaymentsProviderClients\MTNMoMo;
use Illuminate\Support\Str;
use Tests\TestCase;

class MTNMoMoTest extends TestCase
{

   public function _testRequestPayment()
   {   

      $momoParams=[];
      $momoParams['transactionId']="";
      $momoParams['accountNumber']="KCT10482";
      $momoParams['paymentAmount']='1.10';
      $momoParams['urlPrefix']='chambeshi';
      $momoParams['walletNumber']= '260965199175';
      $mtnClient=new MTNMoMo();
      $response = $mtnClient->requestPayment((object)$momoParams);
      $this->assertTrue($response['status']=="SUCCESS");

   }

   public function _testConfirmPayment()
   {   


      $momoParams=[];
      $momoParams['transactionId']="eccab365-3b44-4b51-951c-6a92d2b9ec2a";
      $momoParams['urlPrefix']='lukanga';
      $mtnClient=new MTNMoMo();
      $response = $mtnClient->confirmPayment((object)$momoParams);
      $this->assertTrue($response['status']=="SUCCESS");

   }

   public function _testDeliverNotification()
   {   

      $strClientName="LUKANGA";
      $momoParams=[];
      
      $momoParams['transactionId']="";
      $momoParams['message']="Testing notification delivery for an MTN Transaction. Number : ".
                              $momoParams['transactionId'];
      $momoParams['configs']=[
               'baseURL'=>\env('MTN_BASE_URL'),
               'clientId'=>\env($strClientName.'_MTN_OCPKEY'),
               'clientUserName'=>\env($strClientName.'_MTN_USERNAME'),
               'clientPassword'=>\env($strClientName.'_MTN_PASSWORD'),
               'txCurrency'=>\env('MTN_CURRENCY'),
               'targetEnv'=>\env('MTN_TARGET_ENVIRONMENT')
         ];

      $mtnClient=new MTNMoMo();
      $response = $mtnClient->deliverNotification($momoParams);

      $this->assertTrue($response['status']=="SUCCESS");

   }

}