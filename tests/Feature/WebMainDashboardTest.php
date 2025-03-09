<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebMainDashboardTest extends TestCase
{

   public function _test_get_dashboard(): void
   {

      //Login
      $username = 'kafubudev';
      $password = '1    1';
      $dateFrom = '2025-01-01';
      $dateTo= '2025-01-31';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->withHeaders([
                              'Authorization' => 'Bearer ' . $response['token'],
                        ])->get('/maindashboard?client_id='.$response['client_id'].'&dateFrom='.$dateFrom.'&dateTo='.$dateTo);

      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }


}
