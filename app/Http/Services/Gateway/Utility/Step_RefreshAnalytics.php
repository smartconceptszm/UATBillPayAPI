<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\PaymentsAnalyticsRegularJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_RefreshAnalytics extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
            $paymentStatusArr = [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value];
                                    
            if(in_array($paymentDTO->paymentStatus,$paymentStatusArr)){
               $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
               $dashboardCache = (int)$billpaySettings['DASHBOARD_CACHE_'.strtoupper($paymentDTO->urlPrefix)]; 
               $clientPaymentCount = (int)Cache::get($paymentDTO->client_id.'_PaymentStatusCount');
               if(($clientPaymentCount + 1) >= $dashboardCache){
                  PaymentsAnalyticsRegularJob::dispatch($paymentDTO)
                                                ->delay(Carbon::now()->addSeconds(1))
                                                ->onQueue('high');
                  Cache::put($paymentDTO->client_id.'_PaymentStatusCount', 0, Carbon::now()->addMinutes((int)$billpaySettings['DASHBOARD_CACHE']));
               }else{
                  Cache::increment($paymentDTO->client_id.'_PaymentStatusCount');
               }
            }
      } catch (\Throwable $e) {
         $paymentDTO->error='At refreshing analytics. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}