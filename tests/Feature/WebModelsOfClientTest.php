<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebModelsOfClientTest extends TestCase
{

   public function _test_findWalletsOfClient(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/walletsofclient/'.$response['client_id']);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
