<?php

namespace App\Http\BillPay\Services\MoMo\Utility;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\Payments\PaymentService;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_UpdateTransaction extends EfectivoPipelineContract
{

    private $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService=$paymentService;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {

        try {
            $this->paymentService->update($momoDTO->toPaymentData(),$momoDTO->id);
        } catch (\Throwable $e) {
            $momoDTO->error='At updating payment record. '.$e->getMessage();
        }
        return $momoDTO;

    }
}