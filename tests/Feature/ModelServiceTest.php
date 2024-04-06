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
      
      $modelService = new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService(new \App\Models\ChambeshiAccount());
      $response = $modelService->findOneBy(['AR_Acc'=>'KCT10482']);
      $response->assertStatus(200);

   }

}