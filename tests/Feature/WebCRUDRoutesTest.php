<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebCRUDRoutesTest extends TestCase
{

   public function _test_findOneBy(): void
   {

      //Main Menu
      $username = 'swascodev';
      $password = '1';
      $urlPrefix = 'kafubu';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/clients/findoneby?urlPrefix='.$urlPrefix);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

   public function _test_update_model(): void
   {

      //Main Menu
      $username = 'smartdev';
      $password = '1';
      $id = '39d5f26a-7303-11ee-b8ce-fec6e52a2330';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->put('/clients/'.$id,['balance'=>0]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

   public function _test_get_model(): void
   {

      //Main Menu
      $username = 'smartdev';
      $password = '1';
      $id = '39d5f26a-7303-11ee-b8ce-fec6e52a2330';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/clients/'.$id);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

   public function _test_post_model(): void
   {

      //Main Menu
      $username = 'smartdev';
      $password = '1';

      $client_id = '39d62960-7303-11ee-b8ce-fec6e52a2330';
      $key = '3004';
      $keyValue = 'Bus Station Level';
      $description = 'Bus Station Level';

      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->post('/billingcredentials',[
                                          'client_id' => $client_id,
                                          'key' =>  $key,
                                          'keyValue' =>  $keyValue,
                                          'description' => $description
                                       ]);
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

   public function _test_get_models(): void
   {

      //Main Menu
      $username = 'smartdev';
      $password = '1    1';
      $response = $this->post('/login',['username' => $username,'password' =>$password]);
      $response = $response->json()['data'];

      $response = $this->get('/clients');
      $response = $response->json()['data'];
      $this->assertTrue(\count($response)>0);

   }

}
