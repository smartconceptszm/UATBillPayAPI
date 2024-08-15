<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPaymentReceiptUpdateTest extends TestCase
{

   public function _test_get_payment_update_receipt(): void
   {

      //Main Menu
      $receiptNumber = 'RCPT36359';
      $username = 'swascodev';
      $password = '1';
      $id = '9cb6273b-c654-49f1-9a3d-7e164fef41e4';

      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->put('/receipts/'.$id,['receiptNumber' => $receiptNumber]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
