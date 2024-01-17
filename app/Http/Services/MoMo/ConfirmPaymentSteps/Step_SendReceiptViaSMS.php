<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\SMS\SMSService;
use App\Http\DTOs\SMSTxDTO;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_SendReceiptViaSMS extends EfectivoPipelineContract
{

   public function __construct(
      private SMSService $smsService,
      private SMSTxDTO $smsTxDTO)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if($momoDTO->paymentStatus == 'RECEIPTED' || $momoDTO->paymentStatus == 'PAID | NOT RECEIPTED'){
            if($momoDTO->paymentStatus != 'RECEIPTED'){
               $momoDTO->receipt = "Payment received BUT NOT receipted." . "\n" . 
                              \strtoupper($momoDTO->urlPrefix).
                              " will receipt the payment shortly, please wait.\n";
            }
            $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromMoMoDTO($momoDTO));
            $momoDTO->sms['status'] = $this->smsTxDTO->status;
            $momoDTO->sms['error'] = $this->smsTxDTO->error;
            if($this->smsTxDTO->status == 'DELIVERED' && $momoDTO->paymentStatus == "RECEIPTED"){
               $momoDTO->paymentStatus = "RECEIPT DELIVERED";
            }
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At receipt via sms. '.$e->getMessage();
      }
      return $momoDTO;
   }

}