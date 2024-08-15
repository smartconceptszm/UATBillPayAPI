<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentInititateJobTest extends TestCase
{

   public function _test_initiate_payment(): void
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
         ]);
      $initiatePaymentJob = new \App\Jobs\InitiatePaymentJob($paymentDTO);
      $response  = $initiatePaymentJob->handle(new \App\Http\Services\Gateway\InitiatePayment());
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');

   }
   
}
