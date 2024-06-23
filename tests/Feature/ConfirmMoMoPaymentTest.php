<?php

use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\MoMoDTO;
use Tests\TestCase;

class ConfirmPaymentTest extends TestCase
{

   public function _testConfirmPayment()
   { 

      $momoService = new ConfirmPayment();
      App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,'nkanaPrePaid');
      App::bind(\App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient::class,'MTN');
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,'MockSMSDelivery');
      App::bind(\App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment::class,'ReceiptPrePaidNkana');

      $paymentDTO = new MoMoDTO();
      $paymentDTO = $paymentDTO->fromArray(
         [
               "customerJourney" => "2012*2*0120012000812*98.00*1",
               "mobileNumber" => "260761028631",
               'accountNumber' => '1912000012',
               'meterNumber' => '0120012000812',

               'sessionId' => '100003111',
               'channel' => 'USSD',

               "paymentAmount" => 98.00,
               'receiptAmount' => 98.00,
               'surchargeAmount' => 0,
               'transactionId' => "0237d19b-fec7-41c9-965c-9b6a3e38b7a8",
               "paymentStatus" => 'SUBMITTED',
               'shortCode' => '2021',
               'session_id' => '9b85c8cb-b8b0-4995-b937-4a83e22c7ff7',

               'clientSurcharge' => 'NO',
               'urlPrefix' => 'nkana',
               'mnoName' => 'MTN',
               "district" => 'KITWE',
               "menu_id" => '24aaacd7-d877-11ee-98ed-0a3595084709',
               "client_id" => '39d62961-7303-11ee-b8ce-fec6e52a2330',
               "mno_id" => '0fd6f718-730b-11ee-b8ce-fec6e52a2330',
               "id" => '9b85f2d2-70a5-4fc1-967c-135220956db6'
         ]);
      
      $response = $momoService->handle($paymentDTO);
      $this->assertTrue($response->paymentStatus == 'RECEIPT DELIVERED');
      
   }

}