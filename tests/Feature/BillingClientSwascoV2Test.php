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

    public function _testChangeCustomerNumber()
    {   

        $paymentParams = [
            'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "customerAccount"=> 'CHO0001527',
            "newMobileNumber" => '260979204812',
            "created_at"=> '2024-10-29 06:56:55',
            "mobileNumber"=>'+260979204812'
        ];

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->changeCustomerDetail($paymentParams);

        $this->assertTrue($response['receiptNumber'] == "1234");
    }

    public function _testPostComplaint()
    {   

        $paymentParams = [
            'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "customerAccount"=> 'CHO0001527',
            "compaintCode" => '05B',
            "created_at"=> '2024-10-29 06:56:55',
            "mobileNumber"=>'+260979204812'
        ];

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->postComplaint($paymentParams);

        $this->assertTrue($response['receiptNumber'] == "1234");
    }

    public function _testPostReconnection()
    {   

        $paymentParams = [
            'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "created_at"=> '2024-10-29 06:56:55',
            "customerAccount"=> 'CHO0001527',
            'paymentType' => '4',
            "amount" => '20.00',
            "referenceNumber"=> '12344567',
            "mobileNumber"=>'260977787659',
            'providerName' => 'AIRTEL'
        ];

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->postReconnection($paymentParams);

        $this->assertTrue($response['receiptNumber'] == "1234");
    }


    public function _testPostVacuumTanker()
    {   

        $paymentParams = [
            'client_id'=>'39d6269a-7303-11ee-b8ce-fec6e52a2330',
            "created_at"=> '2024-10-29 06:56:55',
            "customerAccount"=> 'CHO0001527',
            'paymentType' => '69',
            "amount" => '20.00',
            "referenceNumber"=> '12344567',
            "mobileNumber"=>'260977787659',
            'providerName' => 'AIRTEL'
        ];

        $billingClient = new \App\Http\Services\External\BillingClients\SwascoV2(
                                    new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser()
                                );

        $response = $billingClient->postVacuumTanker($paymentParams);

        $this->assertTrue($response['receiptNumber'] == "1234");
    }

}