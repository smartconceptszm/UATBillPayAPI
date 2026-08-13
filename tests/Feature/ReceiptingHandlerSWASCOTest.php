<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptingHandlerSWASCOTest extends TestCase
{

   public function _test_Receonnection(): void
   {

      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      $paymentDTO = $paymentDTO->fromArray(
         [
            "payments_provider_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'customerAccount' => 'CHO0001527',
            "receiptAmount" => 3.00,
            "paymentStatus" => "PAID|NOT RECEIPTED",
            'walletHandler' => 'AIRTEL',
            "mobileNumber" => "260977787659",
            "walletNumber" => "260977787659",
            'session_id' => '9f0d3cd2-26e1-46ab-bc47-316b1bb0f5a6',
            'wallet_id' => 'd617b2d6-7307-11ee-b8ce-fec6e52a2330',
            "client_id" => '39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "menu_id" => '8a2d6cc2-7306-11ee-b8ce-fec6e52a2330',
            'channel' => 'USSD',
            "customerJourney" => "5757*5*1*CHO0001527*260977787659*3.00*1",
            'enquiryHandler' => 'MockBillingClient',
            'sessionId' => '100005063',
            'urlPrefix' => 'swasco',
            'shortCode' => '5757',
            "mno_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'id' => '9f0d3cfc-da99-4b72-9e03-f75cb4890cf5',
            'transactionId'=>'29a73f5c-c133-428d-8827-92fe315374db',
            'created_at'=>'2025-10-21'
         ]);

      $receiptingHandler = new \App\Http\Services\Gateway\ReceiptingHandlers\ReceiptReconnectionSwasco(
                              new \App\Http\Services\External\BillingClients\SwascoPostPaid(
                                 new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential())
                              )
                           );

      $response = $receiptingHandler->handle($paymentDTO);
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
