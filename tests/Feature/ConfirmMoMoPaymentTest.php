<?php

use App\Http\Services\MoMo\ConfirmMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\MoMoDTO;
use Tests\TestCase;

class ConfirmMoMoPaymentTest extends TestCase
{

   public function _testConfirmPayment()
   { 

      $momoService = new ConfirmMoMoPayment();
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,'lukanga');
      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,'MoMoMock');
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,'MockDeliverySMS');

      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromArray(
         [
               "customerJourney" => "2106*1*1101000166*5.67*1",
               "mobileNumber" => "260761028631",
               'accountNumber' => '1101000166',
               'sessionId' => '10000001',
               'channel' => 'USSD',

               "paymentAmount" => 5.67,
               'receiptAmount' => 5.67,
               'surchargeAmount' => 0,
               'transactionId' => "521090d2-2a43-4dc7-ba35-f71083a581de",
               "paymentStatus" => 'INITIATED',
               'clientCode' => 'LgWSSC',
               'session_id' => 1,

               'urlPrefix' => 'lukanga',
               'mnoName' => 'MTN',
               "district" => 'KABWE',
               "menu" => "PayBill",
               "client_id" => 2,
               "mno_id" => 2,
               "id" => 4,
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'RECEIPT DELIVERED');
      
   }

}