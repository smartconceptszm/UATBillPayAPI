<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptingHandlerMulongaTest extends TestCase
{

   public function _test_OtherPayments(): void
   {

      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      $paymentDTO = $paymentDTO->fromArray(
         [
            "payments_provider_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'customerAccount' => 'CHO0001527',
            "receiptAmount" => 5.00,
            "paymentStatus" => "PAID|NOT RECEIPTED",
            'walletHandler' => 'AIRTEL',
            "mobileNumber" => "260977787659",
            "walletNumber" => "260977787659",
            'session_id' => '9f0d3cd2-26e1-46ab-bc47-316b1bb0f5a6',
            'wallet_id' => 'd617b2d6-7307-11ee-b8ce-fec6e52a2330',
            "client_id" => '9c836557-d6f5-497a-919b-c4e61a83536c',
            "menu_id" => 'a1cb0712-22c1-4361-b888-e83e2d7aa1b6',
            'channel' => 'USSD',
            "customerJourney" => "2012*5*1*CHO0001527*260977787659*3.00*1",
            'enquiryHandler' => 'MulongaPostPaid',
            'sessionId' => '100005063',
            'urlPrefix' => 'mulonga',
            'shortCode' => '2012',
            "mno_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'id' => '9f0d3cfc-da99-4b72-9e03-f75cb4890cf5',
            'transactionId'=>'29a73f5c-c133-428d-8827-92fe315374db',
            'created_at'=>'2025-10-21'
         ]);

      $receiptingHandler = new \App\Http\Services\Gateway\ReceiptingHandlers\ReceiptOtherPaymentsMulonga(
                                    new \App\Http\Services\Clients\ClientMenuService( new \App\Models\ClientMenu()),
                                    new \App\Http\Services\External\BillingClients\MulongaPostPaid(
                                       new \App\Http\Services\Clients\BillingCredentialService(
                                          new \App\Models\BillingCredential()
                                       )
                                    )
                                 );

      $response = $receiptingHandler->handle($paymentDTO);
      $this->assertTrue($response);

   }

}
