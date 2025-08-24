<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Exception;

class DailyCommulativeViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                                 ->select(DB::raw('ppt.year,ppt.month,ppt.day,
                                                   SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                   SUM(ppt.totalAmount) as totalRevenue'))
                                 ->whereBetween('ppt.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                                 ->where('ppt.client_id', '=', $dto->client_id)
                                 ->groupBy('ppt.day','ppt.month','ppt.year')
                                 ->orderBy('ppt.day');
         $thePayments = $thePayments->get();

         $period = CarbonPeriod::create($dto->dateFrom, $dto->dateTo);
         if($dto->dateFrom->month != $dto->dateTo->month){
            $period = CarbonPeriod::create($dto->dateFrom, $dto->dateFrom->copy()->endOfMonth());
         }

         $dailyLabels =[];
         $dailyData = [];
         foreach ($period as $date) {
            $daysRecord = $thePayments->firstWhere('day','=',$date->day);
            $dailyLabels[] = $date->day;
            if($daysRecord){
               $dailyData[] = $daysRecord->totalRevenue;
            }else{
               $dailyData[] = 0;
            }
         }

         $cumulativeTotal = 0;
         $cumulativeTrends = [];

         foreach ($dailyData as $amount) {
            $cumulativeTotal += $amount;
            $cumulativeTrends[] = $cumulativeTotal;
         }

         $dailyTrends['labels'] = collect($dailyLabels);
         $colours = ChartColours::getColours(1);
         $dailyTrends['datasets'][] = collect([
                                          'backgroundColor'=>  $colours['backgroundColor'],
                                          'borderColor' =>  $colours['borderColor'],
                                          'pointBackgroundColor' =>  $colours['pointBackgroundColor'],
                                          'pointBorderColor' =>  $colours['pointBorderColor'],
                                          'label' => $dto->dateFrom->copy()->format('M-Y'),
                                          'data' => $cumulativeTrends,
                                          'fill' => false
                                       ]);

         $dateFromPreviousMonth = $dto->dateFrom->copy()->subMonth();
         $dateToPreviousMonth = $dto->dateTo->copy()->subMonth();
         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->select(DB::raw('ppt.year,ppt.month,ppt.day,
                                             SUM(ppt.numberOfTransactions) AS totalTransactions,
                                             SUM(ppt.totalAmount) as totalRevenue'))
                           ->whereBetween('ppt.dateOfTransaction', 
                                             [$dateFromPreviousMonth->copy()->format('Y-m-d'), 
                                                $dateToPreviousMonth->copy()->format('Y-m-d')])
                           ->where('ppt.client_id', '=', $dto->client_id)
                           ->groupBy('day','month','year')
                           ->orderBy('day');
         $dailyTrendsLastMonth = $thePayments->get();

         $period = CarbonPeriod::create($dateFromPreviousMonth, $dateToPreviousMonth);
         if($dateFromPreviousMonth->month != $dateToPreviousMonth->month){
            $period = CarbonPeriod::create($dateFromPreviousMonth, $dateFromPreviousMonth->copy()->endOfMonth());
         }

         $dailyDataLastMonth = [];
         foreach ($period as $date) {
            $daysRecord = $dailyTrendsLastMonth->firstWhere('day','=',$date->day);
            if($daysRecord){
               $dailyDataLastMonth[] = $daysRecord->totalRevenue;
            }else{
               $dailyDataLastMonth[] = 0;
            }
         }

         $cumulativeTotal = 0;
         $cumulativeLastMonthTrends = [];

         foreach ($dailyDataLastMonth as $amount) {
            $cumulativeTotal += $amount;
            $cumulativeLastMonthTrends[] = $cumulativeTotal;
         }

         $colours = ChartColours::getColours(2);
         $dailyTrends['datasets'][] = collect([
                                       'backgroundColor'=> $colours['backgroundColor'],
                                       'borderColor' => $colours['borderColor'],
                                       'pointBackgroundColor' => $colours['pointBackgroundColor'],
                                       'pointBorderColor' => $colours['pointBorderColor'],
                                       'label' => $dateFromPreviousMonth->copy()->format('M-Y'),
                                       'data' => $cumulativeLastMonthTrends,
                                       'fill' => false
                                    ]);

         return $dailyTrends;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }



}
