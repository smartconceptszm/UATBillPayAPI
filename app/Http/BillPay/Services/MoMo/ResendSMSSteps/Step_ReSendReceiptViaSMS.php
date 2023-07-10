<?php

namespace App\Http\BillPay\Services\MoMo\ResendSMSSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\SMSService;
use App\Http\BillPay\DTOs\SMSTxDTO;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_ReSendReceiptViaSMS extends EfectivoPipelineContract
{

   private $smsService;
   private $smsTxDTO;
   public function __construct(SMSTxDTO $smsTxDTO, SMSService $smsService)
   {
      $this->smsService=$smsService;
      $this->smsTxDTO = $smsTxDTO;
   }

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromMoMoDTO($momoDTO));
         $momoDTO->sms['status'] = $this->smsTxDTO->status;
         $momoDTO->sms['error'] = $this->smsTxDTO->error;
         if($this->smsTxDTO->status == 'DELIVERED'){
            if($momoDTO->paymentStatus=="RECEIPTED"){
               $momoDTO->paymentStatus="RECEIPT DELIVERED";
            }
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At resend receipt via sms. '.$e->getMessage();
      }
      return $momoDTO;

   }

}