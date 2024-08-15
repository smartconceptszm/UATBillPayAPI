<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPaymentFailedTest extends TestCase
{

   public function _test_get_payment_failed_ofaclient(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $dateFrom = '2023-10-01';
      $dateTo = '2023-10-31';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/failedpayments?client_id='.$response['client_id'].'&dateFrom='.$dateFrom.'&dateTo='. $dateTo);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

   public function _test_update_payment_failed(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $id = '9bfa8930-579e-4e21-87ae-61216db19bb5';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->put('/failedpayments/'.$id);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
