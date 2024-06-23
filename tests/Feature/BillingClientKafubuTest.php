<?php

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\KafubuPostPaidEnquiry;
use App\Http\Services\External\BillingClients\KafubuPostPaid;
use App\Http\DTOs\UssdDTO;
use Tests\TestCase;

class BillingClientKafubuTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testGetAccount()
    {   

        $txDTO = new UssdDTO();
        $txDTO->accountNumber='1053780';
        $billingClient = new KafubuPostPaidEnquiry(new KafubuPostPaid());
        $response=$billingClient->getAccountDetails($txDTO);
        
        $this->assertTrue($response['accountNumber'] == "1053780");

    }


    public function _testPostPayment()
    {   

        $kafubuBilling = new KafubuPostPaid();

        $response=$kafubuBilling->postPayment(
            [
                'account' => '1053780',
                'balance' => '0',
                'reference' => '10537802406121404',
                'amount' => '1.11',
                'mnoName' => 'MTN'
            ]
        );
        
        $this->assertTrue($response['accountNumber'] == "1053780");

    }

}