<?php

use App\Http\Services\MoMo\ConfirmMoMoPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\MoMoDTO;
use Tests\TestCase;

class ConfirmMoMoPaymentTest extends TestCase
{

   public function testConfirmPayment()
   { 

      $momoService = new ConfirmMoMoPayment();
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,'lukanga');
      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,'MoMoMock');
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,'MockSMSDelivery');
      App::bind(\App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment::class,'MockReceipting');

      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromArray(
         [
               "customerJourney" => "2106*1*1101000166*6.50*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => '1101000166',
               'session_id' => '35634',
               'channel' => 'USSD',

               "paymentAmount" => 6.50,
               'receiptAmount' => 6.50,
               'surchargeAmount' => 0,
               'transactionId' => "D231011T133742A1101000166",
               "paymentStatus" => 'SUBMITTED',
               'shortCode' => '2106',
               'session_id' => 35634,

               'clientSurcharge' => 'NO',
               'urlPrefix' => 'lukanga',
               'mnoName' => 'AIRTEL',
               "district" => 'KABWE',
               "menu_id" => 1,
               "client_id" => 2,
               "mno_id" => 1,
               "id" => 12821,
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'RECEIPT DELIVERED');
      
   }

}