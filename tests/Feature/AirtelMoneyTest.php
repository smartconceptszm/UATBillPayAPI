<?php

use App\Http\Services\External\MoMoClients\AirtelMoney;
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

    public function _testConfirmPayment()
    {   

        $momoParams = (object)[
                        'transactionId'=>'ACH000519D240223T205639',
                        'accountNumber'=>'CH000519',
                        'paymentAmount'=>277.00,
                        'mobileNumber'=>'260979545400',
                        'urlPrefix'=>'swasco'
                    ];

        $airtelClient=new AirtelMoney();
        $response = $airtelClient->confirmPayment($momoParams);
        $this->assertTrue($response->status == "PAID | NOT RECEIPTED");
        //""
    }

    public function _testGenTransactionId()
    {   

        $airtelClient=new AirtelMoney();
        $response = $airtelClient->getTransactionId("1101000166");
        $this->assertTrue(\strlen($response)==25);

    }

}