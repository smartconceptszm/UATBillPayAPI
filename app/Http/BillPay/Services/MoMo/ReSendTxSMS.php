<?php

namespace App\Http\BillPay\Services\MoMo;

use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class ReSendTxSMS
{

    public function handle(BaseDTO $momoDTO):void
    {
        
        $momoDTO =  app(Pipeline::class)
        ->send($momoDTO)
        ->through(
              [
                \App\Http\BillPay\Services\MoMo\ResendSMSSteps\Step_ReSendReceiptViaSMS::class,
                 \App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                 \App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus::class 
              ]
        )
        ->thenReturn();

    }

}
