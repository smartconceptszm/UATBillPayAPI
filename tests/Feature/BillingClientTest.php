<?php

use Tests\TestCase;

class BillingClientTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */


    public function _testGetAccountDetails()
    {   

        $billingClient = \app('lukanga');
        $response=$billingClient->getAccountDetails('1100000001');
        $this->assertTrue($response['accountNumber'] == "1100000001");

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