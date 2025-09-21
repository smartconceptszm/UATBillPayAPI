<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\DashboardPaymentStatusTotals;
use Tests\TestCase;

class UtilityTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_new_code(): void
   {
      
      $theService = new  \App\Http\Services\Promotions\PromotionEntriesNotProcessedService(
                              new \App\Http\Services\Promotions\ProcessPromotionService(),
                              new \App\Http\Services\Payments\PaymentToReviewService(),
                              new \App\Http\Services\Promotions\PromotionService(new \App\Models\Promotion()),
                              new \App\Http\DTOs\PromotionDTO(),
                              new \App\Http\DTOs\MoMoDTO()
                           );
      
      $response = $theService->findAll([
                                    'client_id' => '9eb01c2c-21d6-4bf7-9f88-d2150e9134e9',
                                    'dateFrom' => '2025-06-05',
                                    'dateTo' => '2025-07-31',
                                 ]);

      foreach ($response as $payment) {
         $response2 = $theService->processEntry($payment->id,$payment->promotion_id);
      }

      $this->assertTrue($response);

   }

   /**
    * A basic test example.
    */
   public function _test_a_feature(): void
   {


      $currentEntries = DashboardPaymentStatusTotals::where([
                                          ['dateOfTransaction', '=', '2024-10-24'],
                                          ['client_id', '=', '39d6269a-7303-11ee-b8ce-fec6e52a2330'],
                                       ])
                                       ->pluck('id')
                                       ->toArray();

      DashboardPaymentStatusTotals::destroy($currentEntries);

      $currentEntries = DashboardPaymentStatusTotals::where([
                                                               ['dateOfTransaction', '=', '2024-10-24'],
                                                               ['client_id', '=', '39d6269a-7303-11ee-b8ce-fec6e52a2330'],
                                                            ])
                                                            ->pluck('id')
                                                            ->toArray();

      $this->assertTrue($currentEntries);

   }

}
