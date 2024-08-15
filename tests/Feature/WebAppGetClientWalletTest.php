<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppGetClientWalletTest extends TestCase
{

   public function _test_get_payment_providersofaclient(): void
   {
      //Main Menu
      $payments_provider_id ='0fd6f092-730b-11ee-b8ce-fec6e52a2330';
      $client_id = '39d62460-7303-11ee-b8ce-fec6e52a2330';
      $response = $this->get('/app/clientwallets/findoneby?client_id='.$client_id.'&payments_provider_id='.$payments_provider_id);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
   }


}
