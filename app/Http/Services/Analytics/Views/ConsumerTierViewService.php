<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ConsumerTierViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         $thePayments = DB::table('dashboard_consumer_tier_totals as ctt')
                           ->select(DB::raw('ctt.consumerTier,
                                             SUM(ctt.numberOfTransactions) AS totalTransactions,
                                             SUM(ctt.totalAmount) as totalRevenue'))
                           ->whereBetween('ctt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ctt.client_id', '=', $dto->client_id)
                           ->groupBy('ctt.consumerTier');

         $byConsumerTier = $thePayments->get();

         $consumerTierLabels = $byConsumerTier->map(function ($item) {
                                             return $item->consumerTier;
                                          });

         $consumerTierData = $byConsumerTier->map(function ($item) {
                                          return $item->totalRevenue;
                                       });

         $response = [
                        'consumerTierLabels' => $consumerTierLabels,
                        'consumerTierData' => $consumerTierData,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
