<?php

use App\Http\Services\External\MoMoClients\ZamtelKwacha;
use Tests\TestCase;

class ZamtelKwachaTest extends TestCase
{

    public function _testRequestPayment()
    {   

        $zamtelClient=new ZamtelKwacha();
        $response = $zamtelClient->requestPayment();
        $this->assertTrue($response['status']=="SUCCESS");

    }

    public function _testConfirmPayment()
    {   

        $strClientName="LUKANGA";
        $momoParams=[];
        $momoParams['transactionId']=substr("+260956099652",3,9).date('YmdHis');
        $momoParams['paymentAmount']='1.77';
        $momoParams['mobileNumber']="+260956099652";
        $momoParams['configs']=[
                'baseURL'=>\env('ZAMTEL_BASE_URL'),
                'shortCode'=>\env($strClientName.'_ZAMTEL_SHORTCODE'),
                'clientId'=>\env($strClientName.'_ZAMTEL_THIRDPARTYID'),
                'clientSecret'=>\env($strClientName.'_ZAMTEL_PASSWORD')
            ];
        $zamtelClient=new ZamtelKwacha();
        $response = $zamtelClient->confirmPayment($momoParams);
        $this->assertTrue($response['status']=="SUCCESS");

    }

}