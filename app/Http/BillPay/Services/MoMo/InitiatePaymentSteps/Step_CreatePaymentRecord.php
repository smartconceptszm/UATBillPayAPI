<?php

namespace App\Http\BillPay\Services\MoMo\InitiatePaymentSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\Payments\PaymentService;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_CreatePaymentRecord extends EfectivoPipelineContract
{
    
    private  $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService= $paymentService;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {

        try {
            if($momoDTO->error == ""){
                $payment = $this->paymentService->create($momoDTO->toPaymentData());
                $momoDTO->id = $payment->status;
                $momoDTO->id = $payment->id;
            }
        } catch (\Throwable $e) {
            $momoDTO->error='At creating payment record. '.$e->getMessage();
        }
        return $momoDTO;

    }
}