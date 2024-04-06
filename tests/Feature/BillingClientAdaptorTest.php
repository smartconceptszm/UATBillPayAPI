<?php

use Tests\TestCase;

class BillingClientAdaptorTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */


    public function _testGetPrePaidAccountDetails()
    {   

        $billingClientAdaptor = new App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\ChambeshiPrePaidEnquiry(
            new \App\Http\Services\External\BillingClients\ChambeshiPrePaid(
                new \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService(new \App\Models\ChambeshiPayment())
            )
        );

        $txDTO = new \App\Http\DTOs\UssdDTO();
        $txDTO = $txDTO->fromArray([
                                        'meterNumber'=>'0166209932051',
                                        'paymentAmount'=>25.00
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