<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingClientSWASCOV2Test extends TestCase
{

   public function _test_Get_Account(): void
   {

      //Main Menu
      $params = [
                  'customerAccount' => 'LIV0027040',
                  'client_id' =>'39d6269a-7303-11ee-b8ce-fec6e52a2330'
               ];
      $billingClient =  new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                                 );

      $response = $billingClient->getAccountDetails($params);
      $this->assertTrue($response);

   }

   public function _test_PostPayment(): void
   {

      //Main Menu
      $params = [
                     'referenceNumber' => 'SWASCO2025TEST016',
                     'account' => 'CHO0001527',
                     'amount' => '1.00',
                     'paymentType'=>"01",
                     'receiptType'=>"2",
                     'mobileNumber'=> '260977787659',
                     'client_id' => '39d6269a-7303-11ee-b8ce-fec6e52a2330'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                              );
      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

   public function _test_Reconnection(): void
   {

      //Main Menu
      $params = [
                  'mobileNumber'=> '260977787659',
                  'referenceNumber' => 'SWASCO2025TEST016',
                  'account' => 'CHO0001527',
                  'created_at' => '2025-01-01',
                  'amount' => '1.20',
                  'client_id' => '39d6269a-7303-11ee-b8ce-fec6e52a2330',
                  'paymentType'=>"04",
                  'receiptType'=>"2"
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                              );
      
      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

   public function _test_VacuumTanker(): void
   {

      //Main Menu
      $params = [
                  'mobileNumber'=> '260977787659',
                  'account' => '320008',
                  'created_at' => '2025-01-01',
                  'referenceNumber' => 'SWASCO2025014',
                  'amount' => '1.20',
                  'client_id' => '39d6269a-7303-11ee-b8ce-fec6e52a2330',
                  'paymentType'=>"12",
                  'receiptType'=>"1",
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                              );

      $response = $billingClient->postPayment($params);
      $this->assertTrue($response);

   }

   public function _test_PostComplaint(): void
   {

      //Main Menu
      $params = [
                  'mobileNumber'=> '260977787659',
                  'customerAccount' => 'CHO0001527',
                  'complaintCode' => '01A',
                  'created_at' => '2025-02-01',
                  'client_id' => '39d6269a-7303-11ee-b8ce-fec6e52a2330'
               ];

      $billingClient =  new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                              );

      $response = $billingClient->postComplaint($params);
      $this->assertTrue($response);

   }

}
