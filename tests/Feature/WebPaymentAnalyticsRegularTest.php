<?php

namespace Tests\Feature;


use Tests\TestCase;

class WebPaymentAnalyticsRegularTest extends TestCase
{

   public function _test_get_analytics_regular(): void
   {

      $paymentToReviewService = new \App\Http\Services\Payments\PaymentToReviewService();
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      
      $thePayment = $paymentToReviewService->findById('9bee8d37-f29d-46f3-af42-7c462e8423cd');
      $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));

      $RegularAnalyticsService= new \App\Http\Services\Analytics\RegularAnalyticsService();

      $response = $RegularAnalyticsService->generate( $paymentDTO);

      $this->assertTrue($response);


      // use Illuminate\Support\Carbon;
      // $theDate = Carbon::yesterday();
      
   }

}
