<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MTNSMSClientTest extends TestCase
{

   public function _test_MTN_SMS(): void
   {

      //Main Menu
      $params = ['mobileNumber' =>'260972702707',
                  'message' => 'Test Message from API',
                  'transactionId' => 'ALIV0017400D240913T035853',
                  'channel_id' => 'd617b452-7307-11ee-b8ce-fec6e52a2330'
               ];
      $mtnSMSClient = new \App\Http\Services\External\SMSClients\MTNSMS(new \App\Http\Services\Clients\ClientMnoCredentialsService(new \App\Models\ClientMnoCredentials([])));
      $response = $mtnSMSClient->send($params);
      $this->assertTrue($response);

   }

}
