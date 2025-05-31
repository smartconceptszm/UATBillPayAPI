<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelServiceTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_FindAll(): void
   {

      $client_id= '9eb01c2c-21d6-4bf7-9f88-d2150e9134e9';
      $modelService = new \App\Http\Services\Promotions\PromotionService(new \App\Models\Promotion());
      $activePromo = $modelService->findActivePromotion($client_id);
      $this->assertTrue($activePromo);
      // $client->assertStatus(200);

   }

      /**
    * A basic test example.
    */
    public function _test_FindOneBy(): void
    {
       $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
       $modelService = new \App\Http\Services\Clients\ClientService(new \App\Models\Client());
       $client = $modelService->findOneBy(['urlPrefix'=>'nkana']);
       $testMSISDN = \explode("*", $billpaySettings['APP_ADMIN_MSISDN']."*".$client->testMSISDN);
       $testMSISDN = array_filter($testMSISDN,function($entry){
                                     return $entry !== "";
                                  });
       
       $this->assertTrue($testMSISDN);
       // $client->assertStatus(200);
 
    }

}