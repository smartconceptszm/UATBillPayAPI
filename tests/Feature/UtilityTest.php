<?php

use Tests\TestCase;

use App\Http\Services\External\BillingClients\Chambeshi\ChambeshiAccountService;
use App\Http\Services\Payments\PaymentService;
Use App\Models\Payment;

class UtilityTest extends TestCase
{

    public function _testChambeshi()
    {   

        $getAccount = new ChambeshiAccountService(new \App\Models\ChambeshiAccount());

        $customer = $getAccount->findOneBy(['AR_Acc' => 'CHL1002']);

        $this->assertTrue($customer['AR_Acc'] == 'CHL1002');

    }

    public function testModelService()
    {   

        $paymentService = new PaymentService(new Payment([]));
        $response = $paymentService->update([
                                                    'mnoTransactionId'=>'',
                                                    'transactionId'=>'9999cc3e-9c1c-46b4-9254-1bc7ae913eb6',
                                                    'payments/9999cc3e-9c1c-46b4-9254-1bc7ae913eb6'=>null,
                                                    'paymentStatus'=>'PAYMENT FAILED'
                                                ]
                ,'9b0ccfd2-955c-4dcf-8d70-7c05024e937c');

        $this->assertTrue($response->paymentStatus == 'PAYMENT FAIKLED');

    }

}