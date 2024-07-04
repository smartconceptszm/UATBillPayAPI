<?php

use App\Http\Services\External\PaymentsProviderClients\DPOPay;
use Tests\TestCase;

class DPOPayTest extends TestCase
{

    public function _testRequestPayment()
    {   
        //
        $dpoPayClient=new DPOPay(new \App\Http\Services\Web\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()));
        $response = $dpoPayClient->requestPayment((object)[
                        'wallet_id'=>'9b9fe666-1ff3-11ef-b077-8db5e354f7db',
                        'transactionDate' => '2024/07/02 13:45',
                        'paymentAmount' => 10.00,
                        'creditCardNumber' => '5436886269848367',
                        'cardHolderName' => 'John Doe',
                        'cardExpiry' => '1224',
                        'cardCVV' => '123',
                    ]);
        $this->assertTrue($response['status']=="SUCCESS");

    }

    public function _testConfirmPayment()
    {   
        //
        $dpoPayClient=new DPOPay(new \App\Http\Services\Web\Clients\ClientWalletCredentialsService(new \App\Models\ClientWalletCredential()));
        $response = $dpoPayClient->confirmPayment((object)[
                        'wallet_id'=>'9b9fe666-1ff3-11ef-b077-8db5e354f7db',
                        'transactionId' => '0996278A-4AC0-4C2F-9D0D-C6083DF13430',
                    ]);
        $this->assertTrue($response['status']=="SUCCESS");

    }


}