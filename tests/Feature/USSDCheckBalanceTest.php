<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class USSDCheckBalanceTest extends TestCase
{

   public function _test_airtel(): void
   {
      //Main Menu
      $urlPrefix = 'swasco';
      $menuNo = '2';
      $sessionId = '100005075';
      $customerAccount = 'liv0005559';
      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=2022&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=1');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT='.$menuNo.'&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT='.$customerAccount.'&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=1&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=12&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=1&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

   }

   public function _test_mtn(): void
   {
      //Check Ballance Complex
      $urlPrefix = 'nkana';
      $sessionId = '100005065';
      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=2021&sessionId='.$sessionId.'&isnewrequest=1');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=3&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=148125721&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=1&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=12&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=1&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

   }

   public function _test_zamtel(): void
   {
      //Main Menu
      $urlPrefix = 'swasco';
      $sessionId = '100005059';
      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=1&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757%23');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*2');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*2*liv0005559');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*2*liv0005559*1');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*2*liv0005559*1*12');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*2*liv0005559*1*12*1');
      $response->assertStatus(200);

   }

}
