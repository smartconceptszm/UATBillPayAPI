<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingClientLuapulaTest extends TestCase
{

   public function _test_PrePaid_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '0120210362659',
                  'paymentAmount' => '20.00',
                  'client_id' =>'9ecea2d9-803b-4020-a5a8-66592c2b8224'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\LuapulaPrePaid(
         new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
         new \App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor(),
         new \App\Http\Services\External\BillingClients\LuapulaPostPaid(new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()))
      );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PrePaid_Get_Token(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => '0120210362659',
                  'transactionId' => \now()->timestamp.\strtoupper(Str::random(6)),
                  'paymentAmount' => '2.00',
                  'client_id' =>'9eb01c2c-21d6-4bf7-9f88-d2150e9134e9'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\LuapulaPrePaid(
         new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
         new \App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor(),
         new \App\Http\Services\External\BillingClients\LuapulaPostPaid(new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()))
      );

      $response = $billingClient->generateToken($params);
      $this->assertTrue($response);

   }

   public function _test_PostPaid_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => 'MSA00001',
                  'client_id' =>'9ecea2d9-803b-4020-a5a8-66592c2b8224'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\LuapulaPostPaid(
         new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
      );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }


   public function _test_PostPaid_PostPayment(): void
   {

      //Main Menu
      $params = [
                  'client_id' =>'9ecea2d9-803b-4020-a5a8-66592c2b8224',
                  "txnDate"=> Carbon::now()->format('Y-m-d'),
                  'payment_provider' => 'airtel_money',
                  'payer_msisdn'=> '260977787659',
                  'account' => 'MSA00002',
                  'amount' => '2.00',
                  'txnId'=>"FJB210815752938UAT2",
                  'ReceiptNo'=>"MSA00001".\now()->timestamp,
                  "transDesc"=>"PostPaid"
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\LuapulaPostPaid(
                  new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
               );
      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

}
