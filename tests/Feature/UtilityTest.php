<?php

use Tests\TestCase;

Use App\Models\Payment;

class UtilityTest extends TestCase
{

    public function _testFunction()
    {   

        $paymentHistoryService = new \App\Http\Services\Web\Payments\PaymentHistoryService();

        $payment = $paymentHistoryService->getLatestToken(
                            [
                            'meterNumber' => "0120210638835",
                            'client_id' => "39d62460-7303-11ee-b8ce-fec6e52a2330"
                            ]
                        );

        $this->assertTrue(strlen($payment->receipt) == 24);

    }

}