<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class RevenueCollectorViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         //7. RevenueCollector Totals for Month
            $theCollectorPayments = DB::table('dashboard_revenue_collector_totals as drct')
                  ->select(DB::raw('drct.revenueCollector,
                                    SUM(drct.numberOfTransactions) AS totalTransactions,
                                    SUM(drct.totalAmount) as totalRevenue'))
                  ->whereBetween('drct.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                  ->where('drct.client_id', '=', $dto->client_id)
                  ->groupBy('drct.revenueCollector');
            $byRevenueCollector = $theCollectorPayments->get();
            $revenueCollectorLabels = $byRevenueCollector->map(function ($item) {
                                                return $item->revenueCollector;
                                             });
            $revenueCollectorData = $byRevenueCollector->map(function ($item) {
                                             return $item->totalRevenue;
                                          });
         //
         $response = [
                        'revenueCollectorLabels' =>$revenueCollectorLabels,
                        'revenueCollectorData' =>$revenueCollectorData 
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
