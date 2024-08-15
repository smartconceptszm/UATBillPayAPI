<?php

use Tests\TestCase;
use Illuminate\Support\Str;

class BillingPrepaidClientTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testGetPrePaidAccountDetails()
    {   

        $billingClient = new \App\Http\Services\External\BillingClients\LukangaPrePaid(
                                new \App\Http\Services\Web\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                new App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor()
                            );

        $response = $billingClient->getAccountDetails([
                                        'customerAccount'=>'0120210635195',
                                        'client_id'=>'39d62460-7303-11ee-b8ce-fec6e52a2330',
                                        'paymentAmount'=>20000.00,
                                    ]);

        $this->assertTrue($response['customerAccount'] == "KMH42801");

    }

    public function _testPostPayment()
    {   

        $tokenParams = [
            "meter_number"=> '0166200096872',
            "total_paid" => '20.00',
            "debt_percent"=> 50
        ];
        $billingClient = \app('chambeshiPrePaid');
        $response=$billingClient->generateToken($tokenParams);
        $this->assertTrue($response['customerAccount'] == "040010151");
    }


}