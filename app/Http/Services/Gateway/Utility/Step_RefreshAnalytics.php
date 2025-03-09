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
         if ($this->isPaymentStatusEligible($paymentDTO->paymentStatus)) {
               $this->processAnalytics($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At refreshing analytics. ' . $e->getMessage();
      }

      return $paymentDTO;
   }

   private function isPaymentStatusEligible(string $paymentStatus): bool
   {
      $eligibleStatuses = [
         PaymentStatusEnum::NoToken->value,
         PaymentStatusEnum::Paid->value,
         PaymentStatusEnum::Receipted->value,
         PaymentStatusEnum::Receipt_Delivered->value
      ];

      return in_array($paymentStatus, $eligibleStatuses);
   }

   private function processAnalytics(BaseDTO $paymentDTO): void
   {
      $today = Carbon::today()->toDateString();
      $txDate = Carbon::parse($paymentDTO->created_at)->toDateString();

      if ($txDate === $today) {
         $this->handleTodayTransaction($paymentDTO);
      } else {
         $this->dispatchAnalyticsJob($paymentDTO);
      }
   }

   private function handleTodayTransaction(BaseDTO $paymentDTO): void
   {
      $billpaySettings = $this->getBillpaySettings();
      $dashboardCache = $this->getDashboardCacheLimit($paymentDTO->urlPrefix, $billpaySettings);

      $clientPaymentCountKey = $this->getClientPaymentCountKey($paymentDTO->client_id);
      $clientPaymentCount = (int) Cache::get($clientPaymentCountKey, 0);

      if (($clientPaymentCount + 1) >= $dashboardCache) {
         $this->dispatchAnalyticsJob($paymentDTO);
         $this->resetClientPaymentCount($clientPaymentCountKey, $billpaySettings['DASHBOARD_CACHE']);
      } else {
         Cache::increment($clientPaymentCountKey);
      }
   }

   private function getBillpaySettings(): array
   {
      return json_decode(Cache::get('billpaySettings', json_encode([])), true);
   }

   private function getDashboardCacheLimit(string $urlPrefix, array $billpaySettings): int
   {
      return (int) ($billpaySettings['DASHBOARD_CACHE_' . strtoupper($urlPrefix)] ?? 0);
   }

   private function getClientPaymentCountKey(string $clientId): string
   {
      return $clientId . '_PaymentStatusCount';
   }

   private function resetClientPaymentCount(string $key, int $minutes): void
   {
      Cache::put($key, 0, Carbon::now()->addMinutes($minutes));
   }

   private function dispatchAnalyticsJob(BaseDTO $paymentDTO): void
   {
      PaymentsAnalyticsRegularJob::dispatch($paymentDTO)
                                    ->delay(Carbon::now()->addSeconds(1))
                                    ->onQueue('high');
   }

}