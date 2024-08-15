<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentsStatusTest extends TestCase
{

   public function _test_get_payment_status(): void
   {


      //Main Menu
      $session_id = '9ca9a283-22d3-4912-9ad3-7e7df626759a';
      $response = $this->get('/app/payments/'.$session_id);
      $response = $response->json()['data'];
      $this->assertTrue($response['paymentStatus'] == "RECEIPT DELIVERED");
   }


}
