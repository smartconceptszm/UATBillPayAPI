<?php

use App\Http\BillPay\Services\External\BillingClients\BillingClientBinderService;
use App\Http\BillPay\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use App\Http\BillPay\Services\MoMo\Utility\StepService_GetPaymentStatus;
use App\Http\BillPay\Services\External\MoMoClients\MoMoMock;
use App\Http\BillPay\Services\MoMo\ConfirmMoMoPayment;
use App\Http\BillPay\DTOs\MoMoDTO;
use Tests\TestCase;

class ConfirmMoMoPaymentTest extends TestCase
{

    public function testConfirmPayment()
    { 

        $billingServiceBinder = new BillingClientBinderService();
        $momoServiceBinder = new MoMoClientBinderService();
        $smsServiceBinder = new SMSClientBinderService();

        $billingServiceBinder->bind('lukanga');
        $momoServiceBinder->bind('MoMoMock');
        $smsServiceBinder->bind('MockDeliverySMS');

        $momoClient = new MoMoMock();
        $getPaymentStatus = new StepService_GetPaymentStatus($momoClient);
        $momoService = new ConfirmMoMoPayment($getPaymentStatus);

        $momoDTO = new MoMoDTO();
        $momoDTO = $momoDTO->fromArray(
            [
                "customerJourney" => "2106*1*1101000166*7.00*1",
                "mobileNumber" => "260761028631",
                'accountNumber' => '1101000166',
                'sessionId' => '10000003',
                'channel' => 'USSD',

                "paymentAmount" => 7,
                'receiptAmount' => 7,
                'surchargeAmount' => 0,
                'transactionId' => "533eb81e-b58a-44c6-a3b7-bb517a69fcab",
                "paymentStatus" => 'INITIATED',
                'clientCode' => 'LgWSSC',
                'session_id' => 3,

                'urlPrefix' => 'lukanga',
                'mnoName' => 'MTN',
                "district" => 'KABWE',
                "menu" => "PayBill",
                "client_id" => 2,
                "mno_id" => 2,
                "id" => 1,
            ]);
        
        $response = $momoService->handle($momoDTO);
        $this->assertTrue($response->paymentStatus == 'RECEIPT DELIVERED');
        
    }

}