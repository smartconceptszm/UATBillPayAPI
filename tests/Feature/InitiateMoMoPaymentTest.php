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
      $momoDTO = $momoDTO->fromUssdData(
         [
               "customerJourney" => "5757*1*LIV0003066*6.50*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => 'LIV0003066',
               'sessionId' => '100002122',
               'urlPrefix' => 'swasco',
               'mnoName' => 'AIRTEL',
               "district" => 'LIVINGSTONE',
               "menu" => "PayBill",
               "client_id" => 3,
               "mno_id" => 2,
               "id" => 35630,
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}