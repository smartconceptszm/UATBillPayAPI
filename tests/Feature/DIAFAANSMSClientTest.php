<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DIAFAANSMSClientTest extends TestCase
{

   public function _test_MTN_SMS(): void
   {

      //Main Menu
      $params = [
                  'sms_provider_id' => '9f903c76-d907-4fa9-9683-ff0a6f185300',
                  'mobileNumber' =>'260972702707',
                  'mno_id' => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
                  'message' => 'Test message from DIAFAAN',
                  'transactionId' => 'ALIV0017400D240913T035853',
                  'channel_id' => '9f903d6e-19b8-489f-89e0-0e75c8545e38'
               ];
      $mtnSMSClient =  new \App\Http\Services\External\SMSClients\DiafaanSMS(
          new \App\Http\Services\Clients\SMSChannelCredentialsService(new \App\Models\SMSChannelCredentials()),
          new \App\Http\Services\Clients\SMSProviderCredentialService(new \App\Models\SMSProviderCredential())
      );
      $response = $mtnSMSClient->send($params);
      $this->assertTrue($response);

   }

}
