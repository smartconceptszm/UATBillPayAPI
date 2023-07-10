<?php

namespace App\Http\BillPay\Services\MoMo\Utility;

use App\Http\BillPay\Services\External\MoMoClients\IMoMoClient;
use App\Http\BillPay\DTOs\BaseDTO;

class StepService_GetPaymentStatus
{

    private $momoClient;
    public function __construct(IMoMoClient $momoClient)
    {
        $this->momoClient=$momoClient;
    }

    public function handle(BaseDTO $momoDTO):BaseDTO
    {
        
        $mnoResponse = $this->momoClient->confirmPayment($momoDTO->toMoMoParams());
        $momoDTO->mnoTransactionId = $mnoResponse->mnoTransactionId;
        $momoDTO->paymentStatus = $mnoResponse->status;
        $momoDTO->error = $mnoResponse->error;
        if (\strpos($momoDTO->error,'PENDING')){
            $momoDTO->error = $momoDTO->mnoName." response: PENDING";
        }
        return $momoDTO;

    }

}