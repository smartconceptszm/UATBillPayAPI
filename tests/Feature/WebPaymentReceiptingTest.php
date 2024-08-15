<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPaymentReceiptingTest extends TestCase
{

   public function _test_get_payment_receipting(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $id = '9cb6273b-c654-49f1-9a3d-7e164fef41e4';

      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->post('/receipts',['id' => $id]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
