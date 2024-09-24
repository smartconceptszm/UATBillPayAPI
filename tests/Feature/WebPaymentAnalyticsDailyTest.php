<?php

namespace Tests\Feature;


use Tests\TestCase;

class WebPaymentAnalyticsDailyTest extends TestCase
{

   public function test_get_analytics_daily(): void
   {

      $paymentToReviewService = new \App\Http\Services\Web\Payments\PaymentToReviewService();
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      
      $thePayment = $paymentToReviewService->findById('9b319970-edb0-43f5-aff6-91b14be0c892');
      $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));

      $analyticsDaily = new \App\Http\Services\Web\Payments\AnalyticsDaily();

      $response = $analyticsDaily->handle( $paymentDTO);

      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
   }

}
