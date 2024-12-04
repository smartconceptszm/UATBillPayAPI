<?php

namespace App\Http\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class UserDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFrom = $dateFrom->format('Y-m-d');
         $dateTo = Carbon::parse($dto->dateTo);
         $dateTo = $dateTo->format('Y-m-d');

         $endOfMonth = Carbon::parse($dto->dateTo);
         $endOfMonth = $endOfMonth->copy()->endOfMonth();;
         $theYear = (string)$endOfMonth->year;
         $theMonth = $endOfMonth->month;
         
         //1. Payments Provider Totals for Month
            $thePayments = DB::table('dashboard_revenue_collector_totals as drct')
                              ->select(DB::raw('drct.revenueCollector,
                                                   SUM(drct.numberOfTransactions) AS totalTransactions,
                                                      SUM(drct.totalAmount) as totalRevenue'))
                              ->whereBetween('drct.dateOfTransaction', [$dateFrom, $dateTo])
                              ->where('drct.client_id', '=', $dto->client_id)
                              ->where('drct.revenueCollector', '=', $dto->fullnames)
                              ->groupBy('drct.revenueCollector')
                              ->orderByDesc('totalRevenue')
                              ->get();

            $totalRevenue = $thePayments->reduce(function ($totalRevenue, $item) {
                                                return $totalRevenue + $item->totalRevenue;
                                          });

            $totalRevenue = $totalRevenue?$totalRevenue:0;

            $paymentsSummary = [
                                    'revenueCollector'=>$dto->fullnames,
                                    'totalAmount'=>$totalRevenue,
                                    'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                                 ];
         //
         //2. Daily Totals over the Month
            $thePayments = DB::table('dashboard_revenue_collector_totals as drct')
                              ->select(DB::raw('drct.year,drct.month,drct.day,
                                                SUM(drct.numberOfTransactions) AS totalTransactions,
                                                SUM(drct.totalAmount) as totalRevenue'))
                              ->where('drct.year', '=',  $theYear)
                              ->where('drct.month', '=',  $theMonth)
                              ->where('drct.client_id', '=', $dto->client_id)
                              ->where('drct.revenueCollector', '=', $dto->fullnames)
                              ->groupBy('day','month','year')
                              ->orderBy('day');
            $dailyTrends = $thePayments->get();
            $dailyLabels = $dailyTrends->map(function ($item) {
                                                   return $item->day;
                                             });
            $dailyData = $dailyTrends->map(function ($item) {
                                                return $item->totalRevenue;
                                          });
         //
         $response = [
                        'paymentsSummary' => $paymentsSummary,
                        'dailyLabels' => $dailyLabels,
                        'dailyData' =>$dailyData
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
