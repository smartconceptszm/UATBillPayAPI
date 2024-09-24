<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\PaymentsAnalytics;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         if($paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED'){
            $dashboardCache = (int)\env('DASHBOARD_CACHE'); 
            $clientPaymentCount = (int)Cache::get($paymentDTO->client_id.'_PaymentStatusCount');
            if(($clientPaymentCount + 1) == $dashboardCache){
               Queue::later(Carbon::now()->addSeconds((int)\env($paymentDTO->walletHandler.'_PAYSTATUS_CHECK')),new PaymentsAnalytics($paymentDTO),'','high');
               Cache::put($paymentDTO->client_id.'_PaymentStatusCount', 1, Carbon::now()->addMinutes((int)\env('DASHBOARD_CACHE')));
            }else{
               Cache::increment($paymentDTO->client_id.'_PaymentStatusCount');
            }
            $paymentDTO = $this->receiptPayment->handle($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}