<?php

use Tests\TestCase;

class BillingClientAdaptorTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */


    public function testGetPrePaidAccountDetails()
    {   

        $billingClientAdaptor = new App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\NkanaPrePaidEnquiry(
            new \App\Http\Services\External\BillingClients\NkanaPrePaid(new \App\Http\Services\External\BillingClients\Nkana\PurchaseEncryptor())
        );

        $txDTO = new \App\Http\DTOs\UssdDTO();
        $txDTO = $txDTO->fromArray([
                                        'meterNumber'=>'0120030047597',
                                        'paymentAmount'=>30.00
                                    ]);

        $response = $billingClientAdaptor->getAccountDetails($txDTO);

        $this->assertTrue($response['accountNumber'] == "040010151");

    }

    public function _testGetPostPaidAccountDetails()
    {   

        $billingClientAdaptor = new App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\ChambeshiPostPaidEnquiry(
            new App\Http\Services\External\BillingClients\ChambeshiPostPaid(
                new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService(new \App\Models\ChambeshiPayment()), 
                new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService(new \App\Models\ChambeshiAccount())
            )
        );

        $txDTO = new \App\Http\DTOs\UssdDTO();
        $txDTO = $txDTO->fromArray([]);

        $response = $billingClientAdaptor->getAccountDetails($txDTO);

        $this->assertTrue($response['accountNumber'] == "040010151");

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
        $this->assertTrue($response['accountNumber'] == "040010151");
    }

}