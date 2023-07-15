<?php

use App\Http\BillPay\Services\External\MoMoClients\MoMoClientBinderService;
use App\Http\BillPay\Services\MoMo\InitiateMoMoPayment;
use App\Http\BillPay\DTOs\MoMoDTO;
use Tests\TestCase;

class InitiateMoMoPaymentTest extends TestCase
{


    public function _testInitiatePayment()
    { 

        $serviceBinder = new MoMoClientBinderService();
        $momoService = new InitiateMoMoPayment();

        $serviceBinder->bind('MoMoMock');

        $momoDTO = new MoMoDTO();
        $momoDTO = $momoDTO->fromUssdData(
            [
                "customerJourney" => "2106*1*1101000166*23.00*1",
                "mobileNumber" => "260761028631",
                'accountNumber' => '1101000166',
                'sessionId' => '100002103',
                'urlPrefix' => 'lukanga',
                'mnoName' => 'MTN',
                "district" => 'KABWE',
                "menu" => "PayBill",
                "client_id" => 2,
                "mno_id" => 2,
                "id" => 7458,
            ]);
        
        $response = $momoService->handle($momoDTO);
        $this->assertTrue($response->paymentStatus == 'SUBMITTED');
        
    }

}