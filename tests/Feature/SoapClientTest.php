<?php

use App\Http\Services\External\BillingClients\Lukanga;
use Tests\TestCase;

class SoapClientTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */

    public function _testGetAccount()
    {   

        $LgWSSCClient = new Lukanga();
        $response=$LgWSSCClient->getAccountDetails('5500000241');
        
        $this->assertTrue($response['accountNumber'] == "3303000003");

    }


    public function _testPostPayment()
    {   

        $LgWSSCClient = new Lukanga();

        $response=$LgWSSCClient->postPayment(
            [
                'receiptDate' => "20221208",
                'accountNumber' => '1100001033',
                'reference' => '11000010332212082',
                'incomeCode' => 'zz',
                'paytype' => 'C',
                'recTime' => '',
                'amount' => '5.55',
            ]
        );
        
        $this->assertTrue($response['accountNumber'] == "5501000058");

    }

}