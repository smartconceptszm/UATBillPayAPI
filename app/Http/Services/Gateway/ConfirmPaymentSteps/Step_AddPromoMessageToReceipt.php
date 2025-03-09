<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class Step_AddPromoMessageToReceipt extends EfectivoPipelineContract
{

    protected function stepProcess(BaseDTO $paymentDTO)
    {
        try {
            if ($paymentDTO->paymentStatus == PaymentStatusEnum::Receipted->value) {

                $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
                if($billpaySettings['RECEIPT_PROMO_MESSAGE_ENABLED_'.strtoupper($paymentDTO->urlPrefix)] == 'YES'){
                    if((strlen($paymentDTO->receipt) + strlen($billpaySettings['RECEIPT_PROMO_MESSAGE_'.strtoupper($paymentDTO->urlPrefix)]))<160){
                        $paymentDTO->receipt .= $billpaySettings['RECEIPT_PROMO_MESSAGE_'.strtoupper($paymentDTO->urlPrefix)];
                    }
                }
        
            }
        } catch (\Throwable $e) {
            $paymentDTO->error = 'At Add promo message to receipt step. ' . $e->getMessage();
        }

        return $paymentDTO;
    }

}