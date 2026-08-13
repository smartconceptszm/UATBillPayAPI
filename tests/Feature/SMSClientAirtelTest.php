<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SMSClientAirtelTest extends TestCase
{

   public function _test_AIRTEL_SMS(): void
   {

      //Main Menu
      $params = [
                  'sms_provider_id' => 'a1acf7e6-b7ee-4889-951f-1d378d7d05f0',
                  'mobileNumber' =>'260972702707',
                  'mno_id' => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
                  'message' => "Greetings". "\n"."This afternoon!",
                  'transactionId' => 'ALIV0017400D240913T035853',
                  'channel_id' => 'a1acf9b3-88f3-4f5d-8173-979891370a7a'
               ];

      $mtnSMSClient =  new \App\Http\Services\External\SMSClients\AirtelSMS(
          new \App\Http\Services\Clients\SMSChannelCredentialsService(new \App\Models\SMSChannelCredentials()),
          new \App\Http\Services\Clients\SMSProviderCredentialService(new \App\Models\SMSProviderCredential())
      );

      $response = $mtnSMSClient->send($params);
      $this->assertTrue($response);

   }

}
