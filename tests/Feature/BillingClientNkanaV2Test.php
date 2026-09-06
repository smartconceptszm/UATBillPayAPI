<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingClientNkanaV2Test extends TestCase
{

   public function _test_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '076258744',
                  'client_id' =>'39d62961-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\NkanaPostPaidV2(
         new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential())
      );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PostPayment(): void
   {

      //Main Menu

      $params = [
                  "custkey" => '076258744',
                  "amount" => 1,
                  "clientRefnumber" => "123454322",
                  'client_id' =>'39d62961-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\NkanaPostPaidV2(
         new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential())
      );


      $response = $billingClient->postPayment($params);

      $this->assertTrue($response);

   }

   public function _test_PostOtherPayment(): void
   {

      //Main Menu

      $params = [
                  "selectedId" => "6",
                  "clientAccount" => '076258744',
                  "amount" => 1,
                  "clientRefnumber" => "123454324623454324623454324623454324",
                  'client_id' =>'39d62961-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\NkanaPostPaidV2(
         new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential())
      );


      $response = $billingClient->postOtherPayment($params);

      $this->assertTrue($response);

   }

   public function _test_PostComplaint(): void
   {

      $params = [
                  "custkey" => "076258744b",
                  "postPaid" => "YES",  
                  "complaintDescription" => '01A - Billing',
                  "clientPhoneNumber" => "0972702707",
                  'client_id' =>'39d62961-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\NkanaPostPaidV2(
         new \App\Http\Services\Clients\BillingCredentialService( new \App\Models\BillingCredential())
      );


      $response = $billingClient->postComplaint($params);

      $this->assertTrue($response);

   }

}
