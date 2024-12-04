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

      $client_id= '39d62960-7303-11ee-b8ce-fec6e52a2330';
      $modelService = new \App\Http\Services\Clients\ClientMenuService(new \App\Models\ClientMenu());
      $clientMenus = $modelService->findAll([
                                       ['client_id','=', $client_id],
                                       ['handler','!=', 'ParentMenu'],
                                       ['isPayment','=', 'YES']
                                    ]);
      $this->assertTrue($clientMenus);
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