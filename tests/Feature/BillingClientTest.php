<?php

use Tests\TestCase;

class BillingClientTest extends TestCase
{

    // /**
    //  * @runInSeparateProcess
    //  */


    public function _testGetAccountDetails()
    {   

        $billingClient = \app('nkanaPrePaid');
        $response=$billingClient->getAccountDetails('0120012000812');
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