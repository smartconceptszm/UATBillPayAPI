<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class USSDFaultComplaintTest extends TestCase
{

   public function _test_airtel(): void
   {
      //Main Menu
      $urlPrefix = 'nkana';
      $sessionId = '100005066';
      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=2021&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=1');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=4&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=2&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=1&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=4&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=0120220030395&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=0120220030395&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=1&SESSION_ID='.$sessionId.'&IS_NEW_REQUEST=0');
      $response->assertStatus(200);

   }

   public function _test_mtn(): void
   {
      //Make other payment
      $urlPrefix = 'nkana';
      $sessionId = '100005065';
      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=2106&sessionId='.$sessionId.'&isnewrequest=1');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=2&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=0120210642928&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=50&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/mtn?msisdn=260761028631&subscriberInput=1&sessionId='.$sessionId.'&isnewrequest=0');
      $response->assertStatus(200);

   }

   public function _test_zamtel(): void
   {
      //Main Menu
      $urlPrefix = 'swasco';
      $sessionId = '100005064';
      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=1&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757%23');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*5');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*5*2');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*5*1*23 DAMBWA NORTH');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*5*1*23 DAMBWA NORTH*12');
      $response->assertStatus(200);

      $response = $this->get('/'.$urlPrefix.'/Zamtel?&TransId='.$sessionId.'&Pid=0&RequestType=0&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757*5*1*23 DAMBWA NORTH*12*1');
      $response->assertStatus(200);

   }

}
