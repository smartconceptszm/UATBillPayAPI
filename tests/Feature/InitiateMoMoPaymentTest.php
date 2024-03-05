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
               "customerJourney" => "5757*1*LIV0005559*16.00*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => 'LIV0003066',
               "paymentAmount" => 16.00,
               'session_id' => '9b41aa43-9165-4940-b7fa-19feecb7d2e7',
               'urlPrefix' => 'swasco',
               'shortCode' => '5757',
               'mnoName' => 'AIRTEL',
               "district" => 'LIVINGSTONE',
               'billingClient' => 'swasco',
               'channel' => 'USSD',
               "menu_id" => '8a2d5df4-7306-11ee-b8ce-fec6e52a2330',
               "client_id" => '39d6269a-7303-11ee-b8ce-fec6e52a2330',
               'sessionId' => '10000307',
               "mno_id" => '0fd6f092-730b-11ee-b8ce-fec6e52a2330'
         ]);
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}