<?php

namespace Tests\Feature;
 use Illuminate\Support\Carbon;

use Tests\TestCase;

class WebPaymentAnalyticsMonthlyTest extends TestCase
{

   public function _test_get_analytics_monthly(): void
   {

     
      $theDate = Carbon::create("2024-07-31");
      

      $MonthlyAnalyticsService = new \App\Http\Services\Analytics\MonthlyAnalyticsService(
         new \App\Http\Services\Clients\ClientService( new \App\Models\Client())
      );

      $response = $MonthlyAnalyticsService->generate($theDate);

      $this->assertTrue($response);



   }

}
