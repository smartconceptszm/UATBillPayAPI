<?php

use App\Http\Services\MoMo\InitiateMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\MoMoDTO;
use Tests\TestCase;

class InitiateMoMoPaymentTest extends TestCase
{


   public function _testInitiatePayment()
   { 

      $momoService = new InitiateMoMoPayment();

      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,'MoMoMock');
      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromArray(
         [
               "customerJourney" => "5757*1*LIV0003066*16.00*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => 'LIV0003066',
               "paymentAmount" => 16.00,
               'session_id' => 35720,
               'urlPrefix' => 'swasco',
               'shortCode' => '5757',
               'mnoName' => 'AIRTEL',
               "district" => 'LIVINGSTONE',
               'billingClient' => 'swasco',
               'channel' => 'USSD',
               "menu_id" => 1,
               "client_id" => 3,
               'sessionId' => '100002205',
               "mno_id" => 1
         ]);
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}