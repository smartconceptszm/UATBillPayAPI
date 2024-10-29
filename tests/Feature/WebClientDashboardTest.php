<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebClientDashboardTest extends TestCase
{

   public function _test_get_client_dashboard(): void
   {

      //Login
      $username = 'chambeshidev';
      $password = '1    1';
      $dateFrom = '2024-10-01';
      $dateTo= '2024-10-31';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->withHeaders([
                              'Authorization' => 'Bearer ' . $response['token'],
                        ])->get('/clientdashboard?client_id='.$response['client_id'].'&dateFrom='.$dateFrom.'&dateTo='.$dateTo);

      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }


}
