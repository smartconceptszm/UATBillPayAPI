<?php

use Tests\TestCase;

class ReceiptPaymentTest extends TestCase
{


   public function _testReceiptPayment()
   { 

      $paymentReceiptService = new \App\Http\Services\Payments\PaymentReceiptService(
                                          new \App\Http\Services\Payments\PaymentToReviewService(),
                                          new \App\Http\Services\Clients\ClientMenuService(new \App\Models\ClientMenu([])),
                                          new \App\Http\DTOs\MoMoDTO()
      );

      
      $response = $paymentReceiptService->create(['id'=>'9b93d3cc-9eb1-49ab-a104-f33215d17186']);
      
      $this->assertTrue($response == 'SUBMITTED');
      
   }

}