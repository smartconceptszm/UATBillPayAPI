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
        // new \App\Http\Services\Web\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
        // new App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor()
        $billingClient = new \App\Http\Services\External\BillingClients\ChambeshiPrePaid(
                                    new \App\Http\Services\Web\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\External\BillingClients\Chambeshi\Chambeshi(new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService(new \App\Models\ChambeshiPayment()))
                                );

        $response = $billingClient->getAccountDetails([
                                        'customerAccount'=>'0166209919108',
                                        'client_id'=>'39d62802-7303-11ee-b8ce-fec6e52a2330',
                                        'paymentAmount'=>70.00,
                                    ]);

        $this->assertTrue($response['customerAccount'] == "MPI80570");

    }

    public function _testPostPayment()
    {   

        $tokenParams = [
            "meter_number"=> '0166200096872',
            "total_paid" => '20.00',
            "debt_percent"=> 50
        ];
        $billingClient = new \App\Http\Services\External\BillingClients\ChambeshiPrePaid(
                    new \App\Http\Services\Web\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                    new \App\Http\Services\External\BillingClients\Chambeshi\Chambeshi(new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService(new \App\Models\ChambeshiPayment()))
                );

        $response = $billingClient->getAccountDetails([
                        'customerAccount'=>'0166209919108',
                        'client_id'=>'39d62802-7303-11ee-b8ce-fec6e52a2330',
                        'paymentAmount'=>70.00,
                    ]);

        $this->assertTrue($response['customerAccount'] == "MPI80570");
    }


}