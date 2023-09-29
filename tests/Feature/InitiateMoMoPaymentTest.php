<?php

use App\Http\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\Services\MoMo\InitiateMoMoPayment;
use App\Http\DTOs\MoMoDTO;
use Tests\TestCase;

class InitiateMoMoPaymentTest extends TestCase
{


   public function testInitiatePayment()
   { 

      $serviceBinder = new MoMoClientBinderService();
      $momoService = new InitiateMoMoPayment();

      $serviceBinder->bind('MTN');

      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromUssdData(
         [
               "customerJourney" => "5757*1*CHO0001527*1.45*1",
               "mobileNumber" => "260761028631",
               'accountNumber' => 'CHO0001527',
               'sessionId' => '100002116',
               'urlPrefix' => 'swasco',
               'mnoName' => 'MTN',
               "district" => 'CHOMA',
               "menu" => "PayBill",
               "client_id" => 3,
               "mno_id" => 2,
               "id" => 35630,
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'SUBMITTED');
      
   }

}