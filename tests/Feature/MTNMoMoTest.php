<?php

use App\Http\Services\External\MoMoClients\MTNMoMo;
use Illuminate\Support\Str;
use Tests\TestCase;

class MTNMoMoTest extends TestCase
{

   public function _testRequestPayment()
   {   

      $strClientName="LUKANGA";
      $momoParams=[];
      $momoParams['transactionId']=(string)Str::uuid();
      $momoParams['paymentAmount']='5.67';
      $mobile= \substr('+260761028631',4,9);
      $momoParams['mobileNumber']= $mobile;
      $momoParams['configs']=[
               'baseURL'=>\env('MTN_BASE_URL'),
               'clientId'=>\env($strClientName.'_MTN_OCPKEY'),
               'clientUserName'=>\env($strClientName.'_MTN_USERNAME'),
               'clientPassword'=>\env($strClientName.'_MTN_PASSWORD'),
               'txCurrency'=>\env('MTN_CURRENCY'),
               'targetEnv'=>\env('MTN_TARGET_ENVIRONMENT')
         ];

      $mtnClient=new MTNMoMo();
      $response = $mtnClient->requestPayment($momoParams);

      $this->assertTrue($response['status']=="SUCCESS");

   }

   public function _testConfirmPayment()
   {   

      $strClientName="LUKANGA";
      $momoParams=[];
      $momoParams['transactionId']="dbd36ae6-237b-4f42-8892-54f233492c03";
      $momoParams['configs']=[
               'baseURL'=>\env('MTN_BASE_URL'),
               'clientId'=>\env($strClientName.'_MTN_OCPKEY'),
               'clientUserName'=>\env($strClientName.'_MTN_USERNAME'),
               'clientPassword'=>\env($strClientName.'_MTN_PASSWORD'),
               'txCurrency'=>\env('MTN_CURRENCY'),
               'targetEnv'=>\env('MTN_TARGET_ENVIRONMENT')
         ];

      $mtnClient=new MTNMoMo();
      $response = $mtnClient->confirmPayment($momoParams);

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