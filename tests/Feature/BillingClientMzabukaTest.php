<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingClientMzabukaTest extends TestCase
{

   public function _test_Get_Statement(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '20230000005',
                  'client_id' =>'39d62960-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\MazabukaOnCustomerAccount(
                        new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential()),
                        new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()));
      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PostPayment(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '20230000005',
                  'receiptAmount' =>'5.00',
                  'client_id' =>'39d62960-7303-11ee-b8ce-fec6e52a2330',
                  'transactionId' =>'39d62960-7303-11ee-b8ce-fec6e52a2336',
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\MazabukaOnCustomerAccount(
                        new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential()),
                        new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()));
      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

}
