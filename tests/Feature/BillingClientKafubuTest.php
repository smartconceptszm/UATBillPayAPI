<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingClientKafubuTest extends TestCase
{

   public function _test_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '4002523',
                  'client_id' =>'a1ca6f8c-240b-11ef-98b6-0a3595084709'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\KafubuPostPaid(
                           new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                           new \App\Http\Services\Clients\ClientCustomerService( new \App\Models\ClientCustomer()),
                           new \App\Http\Services\Utility\XMLtoArrayParser()
                        );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PostPayment(): void
   {



   }


}
