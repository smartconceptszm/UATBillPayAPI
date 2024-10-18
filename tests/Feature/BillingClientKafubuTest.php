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
        $txDTO->customerAccount='1053780';
        $txDTO->client_id ='cba1c60c-240f-11ef-b077-8db5e354f7db';
        $billingClient = new KafubuPostPaidEnquiry(
                                    new KafubuPostPaid( new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                    new \App\Http\Services\Utility\XMLtoArrayParser())
                                );
        $response=$billingClient->getAccountDetails($txDTO);
        
        $this->assertTrue($response['customerAccount'] == "1053780");

    }


    public function _testPostPayment()
    {   

        $billingClient = new KafubuPostPaidEnquiry(
                                new KafubuPostPaid( new \App\Http\Services\Clients\BillingCredentialService(new \App\Models\BillingCredential()),
                                new \App\Http\Services\Utility\XMLtoArrayParser())
                            );

        $response=$billingClient->postPayment(
            [
                'account' => '1053780',
                'balance' => '0',
                'reference' => '10537802406121404',
                'amount' => '1.11',
                'mnoName' => 'MTN'
            ]
        );
        
        $this->assertTrue($response['customerAccount'] == "1053780");

    }

}