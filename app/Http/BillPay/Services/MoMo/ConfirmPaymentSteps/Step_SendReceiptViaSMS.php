<?php

namespace App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\SMS\SMSService;
use App\Http\BillPay\DTOs\SMSTxDTO;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_SendReceiptViaSMS extends EfectivoPipelineContract
{

   private $smsService;
   private $smsTxDTO;
   public function __construct(SMSTxDTO $smsTxDTO, SMSService $smsService)
   {
      $this->smsService = $smsService;
      $this->smsTxDTO = $smsTxDTO;
   }

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