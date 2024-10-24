<?php

namespace Tests\Feature;
 use Illuminate\Support\Carbon;

use Tests\TestCase;

class WebPaymentAnalyticsDailyTest extends TestCase
{

   public function _test_get_analytics_daily(): void
   {

     
      $theDate = Carbon::create("2024-09-19");
      

      $DailyAnalyticsService = new \App\Http\Services\Analytics\DailyAnalyticsService(
               new \App\Models\DashboardDailyTotals(),
               new \App\Http\Services\Clients\ClientService(new \App\Models\Client())
            );

      $response = $DailyAnalyticsService->generate($theDate);

      $this->assertTrue($response);



   }

   public function _test_generate_analytics_daily(): void
   {
      //Main Menu
      $username = 'swascodev';
      $password = '1    1';
      $dateFrom = '2024-09-08';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->post('/analytics/daily',['date' => $dateFrom]);

      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);
      
   }

}
