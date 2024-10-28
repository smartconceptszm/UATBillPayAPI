<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Analytics\AnalyticsGeneratorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class RegularAnalyticsService
{

   public function __construct(
         private AnalyticsGeneratorService $analyticsGeneratorService) 
      {}

   public function generate(BaseDTO $paymentDTO)
   {
      
      //Process the request
      try {
         $theDate = Carbon::parse($paymentDTO->created_at);
         $params = [
                     'client_id' => $paymentDTO->client_id,
                     'dateFrom' => $theDate->copy()->startOfDay(),
                     'dateTo' => $theDate->copy()->endOfDay(),
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'theDate' => $theDate];
         return $this->analyticsGeneratorService->generate($params);
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      
      
   }

}
