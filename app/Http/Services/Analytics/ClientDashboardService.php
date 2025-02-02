<?php

namespace App\Http\Services\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Exception;

class ClientDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         $endOfMonth = $dateTo->copy()->endOfMonth();

         //1. Payments Provider Totals for Period
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                   SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                      SUM(ppt.totalAmount) as totalRevenue'))
                              ->whereBetween('ppt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                              ->where('ppt.client_id', '=', $dto->client_id)
                              ->groupBy('paymentsProvider','colour')
                              ->orderByDesc('totalRevenue');
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $byPaymentProvider = $thePayments->get();

            $totalRevenue = $byPaymentProvider->reduce(function ($totalRevenue, $item) {
                                                return $totalRevenue + $item->totalRevenue;
                                          });

            $totalRevenue = $totalRevenue?$totalRevenue:0;

            $paymentsSummary = $byPaymentProvider->map(function ($item) {
                                                      return [
                                                         'paymentsProvider'=>$item->paymentsProvider,
                                                         'totalAmount'=>$item->totalRevenue,
                                                         'colour'=>$item->colour
                                                      ];
                                                });
            $paymentsSummary->prepend([
                                       'paymentsProvider'=>'TOTAL',
                                       'totalAmount'=> $totalRevenue,
                                       'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                                 ]);


         //
         //2. Daily Totals over the Month
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->select(DB::raw('ppt.year,ppt.month,ppt.day,
                                                SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                SUM(ppt.totalAmount) as totalRevenue'))
                              ->whereBetween('ppt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                              ->where('ppt.client_id', '=', $dto->client_id)
                              ->groupBy('day','month','year')
                              ->orderBy('day');
            $dailyTrends = $thePayments->get();

            $period = CarbonPeriod::create($dateFrom, $dateTo);
            if($dateFrom->month != $dateTo->month){
               $period = CarbonPeriod::create($dateFrom, $dateFrom->copy()->endOfMonth());
            }
            $dailyLabels =[];
            $dailyData = [];
            foreach ($period as $date) {
               $daysRecord = $dailyTrends->firstWhere('day','=',$date->day);
               $dailyLabels[] = $date->day;
               if($daysRecord){
                  $dailyData[] = $daysRecord->totalRevenue;
               }else{
                  $dailyData[] = 0;
               }
            }

            $dailyTrends = [
                              [
                                 'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                 'borderColor' => "rgba(75,192,192,1)",
                                 'pointBackgroundColor' => "rgba(75,192,192,1)",
                                 'pointBorderColor' => "rgba(75,192,192,1)",
                                 'label' => "Daily revenue - ".$dateFrom->copy()->format('M-Y'),
                                 'data' => $dailyData
                              ]
                           ];
            
            $cumulativeTotal = 0;
            $cumulativeTrends = [];
            
            foreach ($dailyData as $amount) {
               $cumulativeTotal += $amount;
               $cumulativeTrends[] = $cumulativeTotal;
            }

            $dailyCumulativeTrends = [
                              [
                                 'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                 'borderColor' => "rgba(75,192,192,1)",
                                 'pointBackgroundColor' => "rgba(75,192,192,1)",
                                 'pointBorderColor' => "rgba(75,192,192,1)",
                                 'label' => "Cummulative Daily revenue - ".$dateFrom->copy()->format('M-Y'),
                                 'data' => $cumulativeTrends
                              ]
                           ];


            $dateFromPreviousMonth = $dateFrom->copy()->subMonth();
            $dateToPreviousMonth = $dateTo->copy()->subMonth();
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
               $period = CarbonPeriod::create($dateFrom, $dateFromPreviousMonth->copy()->endOfMonth());
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

            $dailyTrends[] =  [
                                 'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                 'borderColor' => "rgba(248,177,20,0.5)",
                                 'pointBackgroundColor' => "rgba(248,177,20,0.5)",
                                 'pointBorderColor' => "rgba(248,177,20,0.5)",
                                 'label' => "Daily revenue - ".$dateFromPreviousMonth->copy()->format('M-Y'),
                                 'data' => $dailyDataLastMonth
                              ];

            $cumulativeTotal = 0;
            $cumulativeLastMonthTrends = [];
            
            foreach ($dailyDataLastMonth as $amount) {
               $cumulativeTotal += $amount;
               $cumulativeLastMonthTrends[] = $cumulativeTotal;
            }

            $dailyCumulativeTrends[] =  [
                                 'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                 'borderColor' => "rgba(248,177,20,0.5)",
                                 'pointBackgroundColor' => "rgba(248,177,20,0.5)",
                                 'pointBorderColor' => "rgba(248,177,20,0.5)",
                                 'label' => "Daily revenue - ".$dateFromPreviousMonth->copy()->format('M-Y'),
                                 'data' => $cumulativeLastMonthTrends
                              ];

         //
         //3. Monthly Totals Over one Year
            $endOfTransactionsYear = $endOfMonth->copy()->addDay(1);
            $oneYearBack = $endOfTransactionsYear->subYear(1);
            $startingDate = $oneYearBack->startOfMonth();

            $yearOfStartingDate = $startingDate->format('Y');
            $monthOfStartingDate = $startingDate->format('m');
            $monthsCollection = collect([['month'=>$monthOfStartingDate,'year'=>$yearOfStartingDate]]);
            $paymentsTrends = DB::table('dashboard_payments_provider_totals as ppt')
                                    ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                                    ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                         ppt.year,ppt.month,
                                                         SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                            SUM(ppt.totalAmount) as totalRevenue'))
                                    ->where('ppt.client_id', '=', $dto->client_id)
                                    ->where('ppt.year', '=',  $yearOfStartingDate)
                                    ->where('ppt.month', '=',  $monthOfStartingDate);

            for ($i=1; $i < 12; $i++) { 
               $startingDate = $startingDate->addMonth();
               $yearOfStartingDate = $startingDate->format('Y');
               $monthOfStartingDate = $startingDate->format('m');
               $monthsCollection->push(['month'=>$monthOfStartingDate,'year'=>$yearOfStartingDate]);
               $client_id= $dto->client_id;
               $paymentsTrends = $paymentsTrends->orWhere(function (Builder $query) use($yearOfStartingDate,$monthOfStartingDate,$client_id) {
                                                         $query->where('ppt.year','=', $yearOfStartingDate)
                                                               ->where('ppt.month','=', $monthOfStartingDate)
                                                               ->where('ppt.client_id','=', $client_id);
                                                   });
            }
            $paymentsTrends = $paymentsTrends->groupBy('month','year')
                                             ->groupBy('paymentsProvider','colour')
                                             ->orderBy('year')
                                             ->orderBy('month');
            // $theSQLQuery = $paymentsTrends->toSql();
            // $theBindings = $paymentsTrends->getBindings();
            // $rawSql3 = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);

            $paymentsTrends = $paymentsTrends->get();

            
            // $monthsCollection = $paymentsTrends->unique('month');
            $monthlyTotals = $monthsCollection->map(function ($record) use($paymentsTrends){
                                    $recordsForMonth = $paymentsTrends->filter(function ($item) use($record) {
                                                            return $item->month == (int)$record['month'];
                                                      });
                                    if($recordsForMonth->isNotEmpty()){
                                       return $recordsForMonth->reduce(function ($totalAmount, $item) {
                                                                        return $totalAmount + $item->totalRevenue;
                                                                  });
                                    }else{
                                       return 0;
                                    }
                                 });

            $paymentsTrendsLabels  = $monthsCollection->map(function ($record){
                                             $theDate = Carbon::createFromFormat('Y-m-d',$record['year'].'-'.$record['month'].'-01');
                                             return $theDate->format('M Y');
                                          });

            $paymentsTrendsData = $paymentsSummary->map(function ($item) use ($paymentsTrends, $monthlyTotals, $monthsCollection){
                                       if($item['paymentsProvider'] == 'TOTAL'){
                                          return [
                                                   'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                                   'borderColor' => $item['colour'],
                                                   'pointBackgroundColor' => $item['colour'],
                                                   'pointBorderColor' => $item['colour'],
                                                   'label' => $item['paymentsProvider'],
                                                   'data' => $monthlyTotals
                                                ];
                                       }else{
                                          $theData = $monthsCollection->map(function ($monthInFocus) use ($paymentsTrends, $item){
                                                            $providerRecordForMonthInFocus = $paymentsTrends->first(function ($record) use($monthInFocus,$item) {
                                                                                    return (($record->paymentsProvider == $item['paymentsProvider'])
                                                                                             && ((int)$monthInFocus['year']==$record->year)
                                                                                             && ((int)$monthInFocus['month']==$record->month)
                                                                                          );
                                                                                 });
                                                            if($providerRecordForMonthInFocus){
                                                               return $providerRecordForMonthInFocus->totalRevenue;
                                                            }else{
                                                               return 0;
                                                            }
                                                         });
                                          return [
                                                   'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                                   'borderColor' => $item['colour'],
                                                   'pointBackgroundColor' => $item['colour'],
                                                   'pointBorderColor' => $item['colour'],
                                                   'label' => $item['paymentsProvider'],
                                                   'data' => $theData
                                                ];
                                       }

                                 });


         //
         //4. RevenuePoint Totals for Month
            $thePayments = DB::table('dashboard_revenue_point_totals as ddt')
                              ->select(DB::raw('ddt.revenuePoint,
                                                SUM(ddt.numberOfTransactions) AS totalTransactions,
                                                SUM(ddt.totalAmount) as totalRevenue'))
                              ->whereBetween('ddt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                              ->where('ddt.client_id', '=', $dto->client_id)
                              ->groupBy('ddt.revenuePoint');
            $byRevenuePoint= $thePayments->get();
            $revenuePointLabels = $byRevenuePoint->map(function ($item) {
                                                return $item->revenuePoint;
                                             });
            $revenuePointData = $byRevenuePoint->map(function ($item) {
                                             return $item->totalRevenue;
                                          });
         //
         //5. Payment Type Totals for Month
            $thePayments = DB::table('dashboard_payment_type_totals as ptt')
                           ->select(DB::raw('ptt.paymentType,
                                                SUM(ptt.numberOfTransactions) AS totalTransactions,
                                                SUM(ptt.totalAmount) as totalRevenue'))
                           ->whereBetween('ptt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ptt.client_id', '=', $dto->client_id)
                           ->groupBy('paymentType');
            $menuTotals = $thePayments->get();
            $paymentTypeLabels = $menuTotals->map(function ($item) {
                                                return $item->paymentType."(".$item->totalTransactions.")";
                                             });
            $paymentTypeData = $menuTotals->map(function ($item) {
                                                return $item->totalRevenue;
                                             });
         //
         //6. Payments Status Totals for Month
            $thePayments = DB::table('dashboard_payment_status_totals as pst')
                              ->select(DB::raw('pst.paymentStatus,
                                          SUM(pst.numberOfTransactions) AS totalTransactions,
                                          SUM(pst.totalAmount) as totalRevenue'))
                              ->whereBetween('pst.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                              ->where('pst.client_id', '=', $dto->client_id)
                              ->groupBy('paymentStatus')
                              ->orderBy('paymentStatus');
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $thePayments = $thePayments->get();

            $paymentStatusData = $thePayments->map(function ($item) {
                                                      return $item->totalRevenue;
                                                });
            $paymentStatusLabels = $thePayments->map(function ($item) {
                                                   return $item->paymentStatus."(".$item->totalTransactions.")";
                                             });
            $paymentStatusColours = $thePayments->map(function ($item) use($billpaySettings) {
                                                return $billpaySettings[$item->paymentStatus.'_COLOUR'];
                                          });        
         //
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
                        'paymentsSummary' => $paymentsSummary,
                        'dailyTrendsData' => collect($dailyTrends),
                        'dailyTrendsLabels' => collect($dailyLabels),
                        'dailyCumulativeTrendsData' => collect($dailyCumulativeTrends),
                        'paymentsTrendsData' => $paymentsTrendsData,
                        'paymentsTrendsLabels' => $paymentsTrendsLabels,
                        'revenuePointLabels' => $revenuePointLabels,
                        'revenuePointData' => $revenuePointData,
                        'paymentStatusData' => $paymentStatusData,
                        'paymentStatusLabels' => $paymentStatusLabels,
                        'paymentStatusColours' => $paymentStatusColours,
                        'paymentTypeLabels' =>$paymentTypeLabels,
                        'paymentTypeData' =>$paymentTypeData,
                        'revenueCollectorLabels' =>$revenueCollectorLabels,
                        'revenueCollectorData' =>$revenueCollectorData 
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
