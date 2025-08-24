<?php

namespace App\Http\Services\SMS\Generators;

use App\Http\Services\SMS\Generators\SMSAnalyticsGeneratorService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class SMSRegularAnalyticsService
{

   public function __construct(
         private SMSAnalyticsGeneratorService $smsAnalyticsGeneratorService) 
      {}

   public function generate(BaseDTO $smsTxDTO)
   {
      
      //Process the request
      try {
         
         $theDate = Carbon::parse($smsTxDTO->created_at);
         $dateFrom = $theDate->copy()->startOfDay()->format('Y-m-d H:i:s');
         $dateTo = $theDate->copy()->endOfDay()->format('Y-m-d H:i:s');
         $params = [
                     'client_id' => $smsTxDTO->client_id,
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                     'theDate' => $theDate
                  ];
         $this->smsAnalyticsGeneratorService->generate($params);
         
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      
      return true;
      
   }

}
