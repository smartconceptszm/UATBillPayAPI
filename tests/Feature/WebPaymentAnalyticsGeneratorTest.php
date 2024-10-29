<?php

namespace Tests\Feature;
 use Illuminate\Support\Carbon;

use Tests\TestCase;

class WebPaymentAnalyticsGeneratorTest extends TestCase
{

   public function _test_GenerateAnalytics(): void
   {

     
      $theDate = Carbon::create("2024-10-18");
      $dateFrom = $theDate->copy()->startOfDay();
      $dateFrom = $dateFrom->format('Y-m-d H:i:s');
      $dateTo = $theDate->copy()->endOfDay();
      $dateTo = $dateTo->format('Y-m-d H:i:s');


      $params = [
                     'client_id' => '39d62802-7303-11ee-b8ce-fec6e52a2330',
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'theDate' => $theDate,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                  ];

      $analyticsGenerator = new \App\Http\Services\Analytics\AnalyticsGeneratorService();

      $response = $analyticsGenerator->generate($params);

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
