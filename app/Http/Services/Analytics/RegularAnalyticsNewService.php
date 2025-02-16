<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Analytics\AnalyticsGeneratorService;
use App\Http\Services\Clients\DashboardSnippetService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class RegularAnalyticsNewService
{

   public function __construct(
         private AnalyticsGeneratorService $analyticsGeneratorService,
         private DashboardSnippetService $dashboardSnippetService,
         private ClientWalletService $clientWalletService) 
      {}

   public function generate(BaseDTO $paymentDTO)
   {
      
      //Process the request
      try {
         
         $theDate = Carbon::parse($paymentDTO->created_at);
         $dateFrom = $theDate->copy()->startOfDay();
         $dateFrom = $dateFrom->format('Y-m-d H:i:s');
         $dateTo = $theDate->copy()->endOfDay();
         $dateTo = $dateTo->format('Y-m-d H:i:s');

         $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);

         $dashboardSnippets = $this->dashboardSnippetService->findAll(['client_id'=>$clientWallet->client_id]);

         $params = [
                     'client_id' => $clientWallet->client_id,
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                     'theDate' => $theDate
                  ];

         foreach ($dashboardSnippets as $snippet) {
            $generatorService = App::make($snippet->handler);
            $generatorService->generate($params);
         }
         
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      
      return true;
      
   }

}
