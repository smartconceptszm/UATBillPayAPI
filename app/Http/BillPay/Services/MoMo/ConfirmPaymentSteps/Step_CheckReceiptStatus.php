<?php

namespace App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\PaymentService;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_CheckReceiptStatus extends EfectivoPipelineContract
{

    private  $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService= $paymentService;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {
        try {
            if($momoDTO->error == ''){
                $payment = $this->paymentService->findById($momoDTO->id);
                if($payment->receiptNumber != ''){
                    $momoDTO->paymentStatus=$payment->paymentStatus;
                    $momoDTO->receiptNumber=$payment->receiptNumber;
                    $momoDTO->receipt=$payment->receipt;
                    $momoDTO->error = 'Payment already receipted - Session: '.$momoDTO->sessionId; 
                }
            }
        } catch (\Throwable $e) {
            $momoDTO->error='At check payment receipt status. '.$e->getMessage();
        }
        return $momoDTO;

    }
}