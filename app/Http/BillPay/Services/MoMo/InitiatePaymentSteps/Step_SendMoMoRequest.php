<?php

namespace App\Http\BillPay\Services\MoMo\InitiatePaymentSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\External\MoMoClients\IMoMoClient;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_SendMoMoRequest extends EfectivoPipelineContract
{

    private $momoClient;
    public function __construct(IMoMoClient $momoClient)
    {
        $this->momoClient=$momoClient;
    }

    protected function stepProcess(BaseDTO $momoDTO)
    {
        try {
            if($momoDTO->error==''){
                $mnoResponse = $this->momoClient->requestPayment($momoDTO->toMoMoParams());
                $momoDTO->transactionId = $mnoResponse->transactionId;
                $momoDTO->paymentStatus = $mnoResponse->status;
                $momoDTO->error = $mnoResponse->error;
            }
        } catch (\Throwable $e) {
            $momoDTO->error='At send momo request pipeline. '.$e->getMessage();
        }
        return $momoDTO;

    }

}