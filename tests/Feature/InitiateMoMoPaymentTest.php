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

      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,'MTN');
      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromArray(
         [
               "customerJourney" => "2012*2*0120012000812*98.00*1",
               "mobileNumber" => "260761028631",
               'accountNumber' => '1912000012',
               'meterNumber' => '0120012000812',
               "paymentAmount" => 98.00,
               'session_id' => '9b85c8cb-b8b0-4995-b937-4a83e22c7ff7',
               'urlPrefix' => 'nkana',
               'shortCode' => '2021',
               'mnoName' => 'MTN',
               "district" => 'KITWE',
               'billingClient' => 'nkanaPrePaid',
               'channel' => 'USSD',
               "menu_id" => '24aaacd7-d877-11ee-98ed-0a3595084709',
               "client_id" => '39d62961-7303-11ee-b8ce-fec6e52a2330',
               'sessionId' => '100003111',
               "mno_id" => '0fd6f718-730b-11ee-b8ce-fec6e52a2330'
         ]);
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}