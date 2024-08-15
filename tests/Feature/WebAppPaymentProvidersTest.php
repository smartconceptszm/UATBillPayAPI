<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentProvidersTest extends TestCase
{

   public function _test_get_payment_providersofaclient(): void
   {
      //Main Menu
      $client_id = '39d62460-7303-11ee-b8ce-fec6e52a2330';
      $response = $this->get('/app/paymentsprovidersofclient?client_id='.$client_id);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
   }


}
