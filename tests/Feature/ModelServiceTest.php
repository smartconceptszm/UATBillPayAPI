<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelServiceTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_FindOneBy(): void
   {
      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $modelService = new \App\Http\Services\Clients\ClientService(new \App\Models\Client());
      $client = $modelService->findOneBy(['urlPrefix'=>'Nkana']);
      $testMSISDN = \explode("*", $billpaySettings['APP_ADMIN_MSISDN']."*".$client->testMSISDN);
      $testMSISDN = array_filter($testMSISDN,function($entry){
                                    return $entry !== "";
                                 });
      
      $this->assertTrue($testMSISDN);
      // $client->assertStatus(200);

   }

}