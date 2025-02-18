<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Analytics\AnalyticsGeneratorService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class RegularAnalyticsService
{

   public function __construct(
         private AnalyticsGeneratorService $analyticsGeneratorService,
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

         $params = [
                     'client_id' => $clientWallet->client_id,
                     'theMonth' => $theDate->month,
                     'theYear' => $theDate->year,
                     'theDay' => $theDate->day,
                     'dateFrom' => $dateFrom,
                     'dateTo' => $dateTo,
                     'theDate' => $theDate
                  ];
         return $this->analyticsGeneratorService->generate($params);
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      
      
   }

}
