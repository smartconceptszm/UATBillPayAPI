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

      $client_id= '39d62802-7303-11ee-b8ce-fec6e52a2330';
      $payment_id = 'a158bb79-96ca-4b15-a55c-25c3f78d371c';  
      $modelService = new \App\Http\Services\Payments\ReceiptService(new \App\Models\Receipt());
      $receipt1 = $modelService->findOneBy([
                                    'client_id'=>$client_id,
                                    'payment_id'=>$payment_id
                                 ]);

      $payment_id = 'a15653d9-8a4e-408f-b479-77f4dcead265';                              
      $receipt2 = $modelService->findOneBy([
                                    'client_id'=>$client_id,
                                    'payment_id'=>$payment_id
                                 ]);

      $payment_id = 'a1563367-e5a0-4fb6-8c03-52a37a433ca1';                    
      $receipt3 = $modelService->findOneBy([
                                    'client_id'=>$client_id,
                                    'payment_id'=>$payment_id
                                 ]);

      $this->assertTrue($receipt1->id ==  $receipt2->id &&  $receipt2->id  == $receipt3->id);
      
 
    }

}