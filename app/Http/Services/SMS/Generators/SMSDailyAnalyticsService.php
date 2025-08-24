<?php

namespace App\Http\Services\SMS\Generators;

use App\Http\Services\SMS\Generators\SMSAnalyticsGeneratorService;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SMSDailyAnalyticsService
{

   public function __construct(
      private SMSAnalyticsGeneratorService $smsAnalyticsGeneratorService,
      private ClientService $clientService)
   {}
   
   public function generate(Carbon $theDate)
   {
      
      try {
         
         $dateFrom = $theDate->copy()->startOfDay()->format('Y-m-d H:i:s');
         $dateTo = $theDate->copy()->endOfDay()->format('Y-m-d H:i:s');
         $params = [
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                     'theDate' => $theDate,
                     'client_id' => ''
                  ];
         $clients = $this->clientService->findAll(['status'=>'ACTIVE']);

         foreach ($clients as $client) {
            $params['client_id'] = $client->id;
            $this->smsAnalyticsGeneratorService->generate($params);                                 
         }
         
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      Log::info('(SCL) Daily SMS analytics service executed for: '.$theDate->format('d F Y'));
      return true;

   }

}
