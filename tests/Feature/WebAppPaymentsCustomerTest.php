<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAppPaymentsCustomerTest extends TestCase
{

   public function _test_get_customer_details(): void
   {
      //Main Menu
      $client_id = '39d62961-7303-11ee-b8ce-fec6e52a2330';
      $menu_id = 'e6ca709d-d875-11ee-98ed-0a3595084709';
      $customerAccount = '148125721';
      $response = $this->get('/app/customers?client_id='.$client_id.'&customerAccount='.$customerAccount.'&menu_id='.$menu_id);
      $response = $response->json()['data'];
      $this->assertTrue($response['customerAccount'] == $customerAccount  );
   }


}
