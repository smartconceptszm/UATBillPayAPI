<?php

use Tests\TestCase;

class ZamtelKwachaTest extends TestCase
{

    public function _testRequestPayment()
    {  

        $momoParams=[];
        $momoParams['wallet_id']="9d03fefd-3445-45a9-961d-6e9764959f2e";
        $momoParams['transactionId']="";
        $momoParams['customerAccount']="1048506";
        $momoParams['paymentAmount']='1.10';
        $momoParams['walletNumber']= '260958662444';



        $zamKwachaClient = new \App\Http\Services\External\PaymentsProviderClients\ZamtelKwacha(
                                            new \App\Http\Services\Web\Clients\ClientWalletCredentialsService( new \App\Models\ClientWalletCredential()),
                                            new \App\Http\Services\Web\Clients\ClientWalletService(new \App\Models\ClientWallet())
        );

        $response = $zamKwachaClient->requestPayment((object)$momoParams);
        $this->assertTrue($response['status']=="SUCCESS");

    }

    public function testConfirmPayment()
    {   

        $momoParams=[];
        $momoParams['wallet_id']="9d03fefd-3445-45a9-961d-6e9764959f2e";
        $momoParams['transactionId']="";
        $momoParams['customerAccount']="1048506";
        $momoParams['paymentAmount']='1.10';
        $momoParams['walletNumber']= '260958662444';
        $zamKwachaClient = new \App\Http\Services\External\PaymentsProviderClients\ZamtelKwacha(
                                            new \App\Http\Services\Web\Clients\ClientWalletCredentialsService( new \App\Models\ClientWalletCredential()),
                                            new \App\Http\Services\Web\Clients\ClientWalletService(new \App\Models\ClientWallet())
                                );
        $response = $zamKwachaClient->confirmPayment((object)$momoParams);
        $this->assertTrue($response['status']=="SUCCESS");


        
    }

}