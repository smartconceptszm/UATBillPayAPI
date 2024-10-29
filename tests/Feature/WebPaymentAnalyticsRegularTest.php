<?php

namespace Tests\Feature;


use Tests\TestCase;

class WebPaymentAnalyticsRegularTest extends TestCase
{

   public function _test_get_analytics_regular(): void
   {

      $paymentToReviewService = new \App\Http\Services\Payments\PaymentToReviewService();
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();

      $thePayment = $paymentToReviewService->findById('9d5b9ed3-2491-47db-903b-d24cabb0c041');
      $paymentDTO = $paymentDTO->fromArray(\get_object_vars($thePayment));

      $RegularAnalyticsService= new \App\Http\Services\Analytics\RegularAnalyticsService(
                                       new \App\Http\Services\Analytics\AnalyticsGeneratorService(),
                                       new \App\Http\Services\Clients\ClientWalletService(new \App\Models\ClientWallet())
                                    );

      $response = $RegularAnalyticsService->generate( $paymentDTO);

      $this->assertTrue($response);


      // use Illuminate\Support\Carbon;
      // $theDate = Carbon::yesterday();
      
   }

}
