<?php

use Tests\TestCase;

use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService;

class UtilityTest extends TestCase
{

    public function _testChambeshi()
    {   

        $getAccount = new ChambeshiAccountService(new \App\Models\ChambeshiAccount());

        $customer = $getAccount->findOneBy(['AR_Acc' => 'CHL1002']);

        $this->assertTrue($customer['AR_Acc'] == 'CHL1002');

    }

}