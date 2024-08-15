<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HttpAuthTest extends TestCase
{

   /**
    * A basic test example.
    */
   public function _test_userlogin(): void
   {
      //Login
      $response = $this->post('/login', ['username' => 'smartdev','password' => '1']);
      $response->assertStatus(200);

      
      $user = $response->json();

      $response->assertStatus(200);
   }

}
