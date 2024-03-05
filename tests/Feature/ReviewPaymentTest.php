<?php

use App\Http\Services\Payments\PaymentFailedService;
use Tests\TestCase;

class ReviewPaymentTest extends TestCase
{


   public function _testReviewFailedPayment()
   { 

      $paymentFailedService = new PaymentFailedService(
         new \App\Http\Services\Payments\PaymentToReviewService(),
         new \App\Http\Services\Clients\ClientMenuService(New \App\Models\ClientMenu()),
         New  \App\Http\DTOs\MoMoDTO()
      );

      
      $response = $paymentFailedService->update('9b37b319-e8dd-442f-8560-a1e9f93a3190');
      
      $this->assertTrue($response == 'SUBMITTED');
      
   }

}