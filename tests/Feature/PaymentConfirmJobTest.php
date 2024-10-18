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
            'customerAccount' => 'LIV0005559',
            "paymentAmount" => 98.00,
            'walletHandler' => 'AIRTEL',
            "mobileNumber" => "260977787659",
            "walletNumber" => "260977787659",
            'session_id' => '9c9e6dc3-8448-4253-a42a-b10d0e362216',
            'wallet_id' => 'd617b2d6-7307-11ee-b8ce-fec6e52a2330',
            "client_id" => '39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "menu_id" => '8a2d6cc2-7306-11ee-b8ce-fec6e52a2330',
            'channel' => 'USSD',
            "customerJourney" => "5757*5*1*LIV0005559*260977787659*12.00*1",
            'enquiryHandler' => 'swascoPostPaidEnquiry',
            'sessionId' => '100005063',
            'urlPrefix' => 'swasco',
            'shortCode' => '5757',
            "mno_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330',
            'id' => '9c9feebb-83a7-4223-808c-7ea18fabb074',
            'transactionId'=>'79d7cd38-fe4f-4bd6-8417-19c118e539bc'
         ]);
      $confirmPaymentJob = new \App\Jobs\ConfirmPaymentJob($paymentDTO);
      $response  = $confirmPaymentJob->handle(new \App\Http\Services\Gateway\ConfirmPayment(),
                                                new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                                new \App\Http\Services\Clients\ClientMenuService(new \App\Models\ClientMenu)
                                             );
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');

   }

}
