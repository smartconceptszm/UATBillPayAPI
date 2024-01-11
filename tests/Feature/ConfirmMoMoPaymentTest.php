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
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,'swasco');
      App::bind(\App\Http\Services\External\MoMoClients\IMoMoClient::class,'MoMoMock');
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,'MockSMSDelivery');
      App::bind(\App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment::class,'MockReceipting');

      $momoDTO = new MoMoDTO();
      $momoDTO = $momoDTO->fromArray(
         [
               "customerJourney" => "5757*1*LIV0003066*16.00*1",
               "mobileNumber" => "260977787659",
               'accountNumber' => 'LIV0003066',
               'session_id' => '35720',
               'channel' => 'USSD',

               "paymentAmount" => 16.00,
               'receiptAmount' => 16.00,
               'surchargeAmount' => 0,
               'transactionId' => "d8288adb-eaed-4413-982d-d4c0015a3608",
               "paymentStatus" => 'SUBMITTED',
               'shortCode' => '5757',
               'session_id' => 35720,

               'clientSurcharge' => 'NO',
               'urlPrefix' => 'swasco',
               'mnoName' => 'AIRTEL',
               "district" => 'LIVINGSTONE',
               "menu_id" => 1,
               "client_id" => 3,
               "mno_id" => 1,
               "id" => 12837,
         ]);
      
      $response = $momoService->handle($momoDTO);
      $this->assertTrue($response->paymentStatus == 'RECEIPT DELIVERED');
      
   }

}