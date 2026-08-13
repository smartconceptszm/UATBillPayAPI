<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Services\Payments\BatchReceiptingService;

use Tests\TestCase;

class UtilityTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_new_code(): void
   {
      
      $params = [
         'client_id'=>'a1ca6f8c-240b-11ef-98b6-0a3595084709',
         'dateFrom' => "2025-09-15",
         'dateTo' => "2025-09-30",
      ];

      $theClass = new BatchReceiptingService();
      $response = $theClass->create($params);
      $this->assertTrue($response);

   }

   /**
    * A basic test example.
    */
   public function _test_a_feature(): void
   {


   }

}
