<?php

namespace App\Http\Services\Analytics\Old;

use App\Http\Services\Analytics\Old\AnalyticsGeneratorServiceOld;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class DailyAnalyticsServiceOld
{

   public function __construct(
      private AnalyticsGeneratorServiceOld $analyticsGeneratorService,
      private ClientService $clientService)
   {}
   
   public function generate(Carbon $theDate)
   {
      
      try {

         $dateFrom = $theDate->copy()->startOfDay();
         $dateFrom = $dateFrom->format('Y-m-d H:i:s');
         $dateTo = $theDate->copy()->endOfDay();
         $dateTo = $dateTo->format('Y-m-d H:i:s');

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
            $this->analyticsGeneratorService->generate($params);
         }
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      Log::info('(SCL) Daily transaction analytics service executed for: '.$theDate->format('d F Y'));
      return true;

   }

}
