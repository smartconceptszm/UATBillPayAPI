<?php

use Tests\TestCase;
use Illuminate\Support\Str;

class BillingClientSwascoV2Test extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testGetAccountDetails()
    {   

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->getAccountDetails([
                                        'customerAccount'=>'CHO0001527',
                                        'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
                                        'paymentAmount'=>70.00,
                                    ]);

        $this->assertTrue($response['customerAccount'] == "CHO0001527");

    }

    public function _testPostPayment()
    {   

        $paymentParams = [
            'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "customerAccount"=> 'CHO00015276',
            "amount" => '20.00',
            "reference"=> '',
            "mobileNumber"=>'260977787659',
            'providerName' => 'AIRTEL'
        ];

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->postPayment($paymentParams);

        $this->assertTrue($response['receiptNumber'] == "1234");
    }


}