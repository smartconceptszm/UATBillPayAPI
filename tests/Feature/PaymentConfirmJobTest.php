<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentConfirmJobTest extends TestCase
{

   public function _test_confirm_payment(): void
   {
      $paymentDTO = new \App\Http\DTOs\MoMoDTO();
      $paymentDTO = $paymentDTO->fromArray(
         [
            "payments_provider_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'customerAccount' => 'MSA00001',
            "paymentAmount" => 300.00,
            "paymentStatus" => "SUBMITTED",
            'walletHandler' => 'AIRTEL',
            "mobileNumber" => "260977787659",
            "walletNumber" => "260977787659",
            'session_id' => '9f0d3cd2-26e1-46ab-bc47-316b1bb0f5a6',
            'wallet_id' => '9ee6af13-14d9-4769-b173-31a9777bc841',
            "client_id" => '9eb01c2c-21d6-4bf7-9f88-d2150e9134e9',
            "menu_id" => '9eb03c96-9e4d-46f3-8b19-cc94ea57b759',
            'channel' => 'USSD',
            "customerJourney" => "2424*MSA00001*260977787659*300.00*1",
            'enquiryHandler' => 'MockBillingClient',
            'sessionId' => '100005063',
            'urlPrefix' => 'swasco',
            'shortCode' => '5757',
            "mno_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'id' => '9f0d3cfc-da99-4b72-9e03-f75cb4890cf5',
            'transactionId'=>'29a73f5c-c133-428d-8827-92fe315374db'
         ]);
      $confirmPaymentJob = new \App\Jobs\ConfirmPaymentJob($paymentDTO);
      $response  = $confirmPaymentJob->handle(new \App\Http\Services\Gateway\ConfirmPayment( new \App\Http\Services\Clients\ClientMenuService(new \App\Models\ClientMenu())),
                           new \App\Http\Services\Clients\PaymentsProviderCredentialService(new \App\Models\PaymentsProviderCredential())
                     );
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');

   }

}
