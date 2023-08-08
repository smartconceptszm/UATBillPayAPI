<?php

use App\Http\BillPay\Services\External\MoMoClients\AirtelMoney;
use Tests\TestCase;

class AirtelMoneyTest extends TestCase
{

    public function _testRequestPayment()
    {   

        $momoParams = (object)[
            'transactionId'=>'230703195608LIV0011567GDY',
            'accountNumber'=>'LIV0011567',
            'paymentAmount'=>73,
            'mobileNumber'=>'260971644467',
            'urlPrefix'=>'swasco'
        ];
        $airtelClient=new AirtelMoney();
        $response = $airtelClient->requestPayment($momoParams);
        $this->assertTrue($response->status=="SUBMITTED");

    }

    public function testConfirmPayment()
    {   

        $momoParams = (object)[
                        'transactionId'=>'D230725T123739A3302000327',
                        'accountNumber'=>'3302000327',
                        'paymentAmount'=>400,
                        'mobileNumber'=>'260974148749',
                        'urlPrefix'=>'lukanga'
                    ];

        $airtelClient=new AirtelMoney();
        $response = $airtelClient->confirmPayment($momoParams);
        $this->assertTrue($response->status == "PAID | NOT RECEIPTED");
        //""
    }

}