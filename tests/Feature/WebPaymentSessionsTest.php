<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPaymentSessionsTest extends TestCase
{

   public function _test_get_payment_sessions_ofclient(): void
   {
      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $customerAccount = 'CHO0001527';
      $dateFrom = '2024-07-01';
      $dateTo = '2024-07-31';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/sessionpayment?client_id='.$response['client_id'].
                                                '&dateFrom='.$dateFrom.'&dateTo='. $dateTo.
                                                   '&customerAccount='.$customerAccount);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
   }


}
