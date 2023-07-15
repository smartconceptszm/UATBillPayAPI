<?php

namespace App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\SMS\SMSService;
use Illuminate\Support\Facades\Queue;
use App\Http\BillPay\DTOs\SMSTxDTO;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;
use App\Jobs\ReSendTxSMSJob;

class Step_SendReceiptViaSMS extends EfectivoPipelineContract
{
   private $smsService;
   private $smsTxDTO;
   public function __construct(SMSTxDTO $smsTxDTO, SMSService $smsService)
   {
      $this->smsService=$smsService;
      $this->smsTxDTO=$smsTxDTO;
   }

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if(($momoDTO->paymentStatus == 'RECEIPTED') || (\strpos($momoDTO->error,'post payment.'))){
               if($momoDTO->paymentStatus != 'RECEIPTED'){
                  $momoDTO->receipt = "Payment received BUT NOT receipted." . "\n" . 
                                 \strtoupper($momoDTO->urlPrefix).
                                 " will receipt the payment shortly, please wait.\n";
               }
               $this->smsTxDTO = $this->smsService->send($this->smsTxDTO->fromMoMoDTO($momoDTO));
               $momoDTO->sms['status'] = $this->smsTxDTO->status;
               $momoDTO->sms['error'] = $this->smsTxDTO->error;
               if($this->smsTxDTO->status == 'DELIVERED'){
                  if($momoDTO->paymentStatus=="RECEIPTED"){
                     $momoDTO->paymentStatus="RECEIPT DELIVERED";
                  }
               }else{
                  Queue::later(Carbon::now()->addMinutes((int)\env('RESEND_SMS')),new ReSendTxSMSJob($momoDTO));
               }
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At receipt via sms. '.$e->getMessage();
      }
      return $momoDTO;
   }

}