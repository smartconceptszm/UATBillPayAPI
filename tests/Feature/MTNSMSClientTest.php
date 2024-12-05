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
                  'sms_provider_id' => 'eb085dae-b149-11ef-8659-d15a4f7f8c43',
                  'mobileNumber' =>'260972702707',
                  'mno_id' => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
                  'message' => 'Test Message from API',
                  'transactionId' => 'ALIV0017400D240913T035853',
                  'channel_id' => '9da458cb-d5e0-4ddf-a46e-208888a2fe59'
               ];
      $mtnSMSClient =  new \App\Http\Services\External\SMSClients\MTNSMS(
          new \App\Http\Services\Clients\SMSChannelCredentialsService(new \App\Models\SMSChannelCredentials()),
          new \App\Http\Services\Clients\SMSProviderCredentialService(new \App\Models\SMSProviderCredential())
      );
      $response = $mtnSMSClient->send($params);
      $this->assertTrue($response);

   }

}
