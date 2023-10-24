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
               "customerJourney" => "2106*1*1101000166*6.50*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => '1101000166',
               "paymentAmount" => 6.50,
               'session_id' => 35634,
               'urlPrefix' => 'lukanga',
               'shortCode' => '2106',
               'mnoName' => 'AIRTEL',
               "district" => 'KABWE',
               'channel' => 'USSD',
               "menu_id" => 1,
               "client_id" => 2,
               "mno_id" => 1
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}