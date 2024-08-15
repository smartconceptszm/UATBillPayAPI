<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPaymentReceiptBulkDeliveryTest extends TestCase
{

   public function _test_get_payment_bulk_receipt_delivery(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $dateFrom = '2024-07-01';
      $dateTo = '2024-07-31';

      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->post('/batchpaymentreceipts',['dateFrom' => $dateFrom,'dateTo' =>$dateTo]);

      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
