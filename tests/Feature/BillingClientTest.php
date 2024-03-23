<?php

use Tests\TestCase;
use Illuminate\Support\Str;

class BillingClientTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */


    public function _testGetAccountDetails()
    {   

        $billingClient = \app('chambeshiPrePaid');
        $response=$billingClient->getAccountDetails('0166200096872');
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

    public function _testGenerateTokenChambeshi()
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


    public function _testGenerateTokenOther()
    {   


        $tokenParams = [
            "meterNumber"=> '1101000166',
            "paymentAmount" => '100.00',
            "transactionId" => '1711204810BTXGOU'
        ];
        $billingClient = \app('lukangaPrePaid');
        $response=$billingClient->generateToken($tokenParams);
        $this->assertTrue($response['accountNumber'] == "040010151");
    }

    public function _testGetComplaints()
    {   

        $billingClient = \app('lukanga');
        $response=$billingClient->getComplaintTypes();
        
        $this->assertTrue($response['accountNumber'] == "5500000241");

    }

    public function _testGetComplaintSubTypes()
    {   

        $billingClient = \app('lukanga');
        $response=$billingClient->getComplaintSubTypes("1");
        
        $this->assertTrue($response['accountNumber'] == "5501000058");

    }

    public function _testGetComplaintSubType()
    {   
        
        $billingClient = \app('lukanga');
        $response=$billingClient->getComplaintSubType("1","1");
        
        $this->assertTrue($response['accountNumber'] == "5501000058");

    }

}