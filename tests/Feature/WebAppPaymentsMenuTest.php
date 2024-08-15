<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentsMenuTest extends TestCase
{

   public function _test_get_payment_menus(): void
   {

      //Main Menu
      $urlPrefix = 'lukanga';
      $response = $this->get('/app/services?urlPrefix='.$urlPrefix);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
      
   }


}
