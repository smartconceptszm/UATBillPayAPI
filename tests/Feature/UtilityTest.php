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
      
      $theService = new  \App\Http\Services\Analytics\Generators\DashboardMenuIdUpdaterService(
                              new \App\Http\Services\Analytics\Dashboards\DashboardPaymentTypeTotalsService(new \App\Models\DashboardPaymentTypeTotals()),
                              new \App\Http\Services\Clients\ClientMenuService(new \App\Models\ClientMenu())
                           );

      $response = $theService->generate();

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
