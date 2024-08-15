<?php

use Tests\TestCase;

class PaymentSessionTest extends TestCase
{


   public function _testPaymentSession()
   { 

      $paymentSessionService = new \App\Http\Services\Web\Payments\PaymentSessionService();

      
      $response = $paymentSessionService->findAll([
                              'customerAccount' =>'0120210638835',
                              'client_id' => '39d62460-7303-11ee-b8ce-fec6e52a2330'
                           ]);
      
      $this->assertTrue($response == 'SUBMITTED');
      
   }

}