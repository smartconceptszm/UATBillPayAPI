<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MTNSMSClientTest extends TestCase
{

   public function _test_MTN_SMS(): void
   {

      //Main Menu
      $params = [
                  'sms_provider_id' => 'd8023388-b264-11ef-9d8d-0a3595084709',
                  'mobileNumber' =>'260977787659',
                  'mno_id' => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
                  'message' => 'Test Message from API',
                  'transactionId' => 'ALIV0017400D240913T035853',
                  'channel_id' => '9da56ee2-b902-4c72-8aed-b2254ac85177'
               ];
      $mtnSMSClient =  new \App\Http\Services\External\SMSClients\MTNSMS(
          new \App\Http\Services\Clients\SMSChannelCredentialsService(new \App\Models\SMSChannelCredentials()),
          new \App\Http\Services\Clients\SMSProviderCredentialService(new \App\Models\SMSProviderCredential())
      );
      $response = $mtnSMSClient->send($params);
      $this->assertTrue($response);

   }

}
