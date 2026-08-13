<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentHistoryTest extends TestCase
{

   public function _test_get_token(): void
   {
      $criteria = [
                     'customerAccount' => '0120190211215',
                     "client_id" => '9eb01c2c-21d6-4bf7-9f88-d2150e9134e9',
                  ];
      $paymentHistoryService = new \App\Http\Services\Payments\PaymentHistoryService();
      $response  = $paymentHistoryService->getLatestToken($criteria);

      $this->assertTrue($response->tokenNumber == '1234');

   }
   
}
