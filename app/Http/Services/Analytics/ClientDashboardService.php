<?php

namespace App\Http\Services\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ClientDashboardService 
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
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                   SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                      SUM(ppt.totalAmount) as totalRevenue'))
                              ->whereBetween('ppt.dateOfTransaction', [$dateFrom, $dateTo])
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
            $thePayments = DB::table('dashboard_payment_status_totals as pst')
                              ->select(DB::raw('pst.year,pst.month,pst.day,
                                                SUM(pst.numberOfTransactions) AS totalTransactions,
                                                SUM(pst.totalAmount) as totalRevenue'))
                              ->where('pst.year', '=',  $theYear)
                              ->where('pst.month', '=',  $theMonth)
                              ->where('pst.client_id', '=', $dto->client_id)
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
         //3 Monthly Totals Over one Year
            // $startDate  = $endOfMonth->copy()->addMonth(1);
            $startDate = $endOfMonth->copy()->addDay(1);
            $startDate = $startDate->subYear(1);
            $startDate = $startDate->startOfMonth();
            $myYear = $startDate->format('Y');
            $myMonth = $startDate->format('m');
            $monthsCollection = collect([['month'=>$myMonth,'year'=>$myYear]]);
            $paymentsTrends = DB::table('dashboard_payments_provider_totals as ppt')
                                    ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                                    ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                         ppt.year,ppt.month,
                                                         SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                            SUM(ppt.totalAmount) as totalRevenue'))
                                    ->where('ppt.client_id', '=', $dto->client_id)
                                    ->where('ppt.year', '=',  $myYear)
                                    ->where('ppt.month', '=',  $myMonth);

            for ($i=1; $i < 12; $i++) { 
               $startDate = $startDate->addMonth();
               $myYear = $startDate->format('Y');
               $myMonth = $startDate->format('m');
               $monthsCollection->push(['month'=>$myMonth,'year'=>$myYear]);
               $client_id= $dto->client_id;
               $paymentsTrends = $paymentsTrends->orWhere(function (Builder $query) use($myYear,$myMonth,$client_id) {
                                                         $query->where('ppt.year','=', $myYear)
                                                               ->where('ppt.month','=', $myMonth)
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
         //4. District Totals for Month
            $thePayments = DB::table('dashboard_district_totals as ddt')
                              ->select(DB::raw('ddt.district,
                                                SUM(ddt.numberOfTransactions) AS totalTransactions,
                                                SUM(ddt.totalAmount) as totalRevenue'))
                              ->whereBetween('ddt.dateOfTransaction', [$dateFrom, $dateTo])
                              ->where('ddt.client_id', '=', $dto->client_id)
                              ->groupBy('ddt.district');
            $byDistrict = $thePayments->get();
            $districtLabels = $byDistrict->map(function ($item) {
                                                return $item->district;
                                             });
            $districtData = $byDistrict->map(function ($item) {
                                             return $item->totalRevenue;
                                          });
         //
         //5. Payment Type Totals for Month
            $thePayments = DB::table('dashboard_payment_type_totals as ptt')
                           ->select(DB::raw('ptt.paymentType,
                                                SUM(ptt.numberOfTransactions) AS totalTransactions,
                                                SUM(ptt.totalAmount) as totalRevenue'))
                           ->whereBetween('ptt.dateOfTransaction', [$dateFrom, $dateTo])
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
                              ->whereBetween('pst.dateOfTransaction', [$dateFrom, $dateTo])
                              ->where('pst.client_id', '=', $dto->client_id)
                              ->groupBy('paymentStatus')
                              ->orderBy('paymentStatus');
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
         $response = [
                        'paymentsSummary' => $paymentsSummary,
                        'dailyLabels' => $dailyLabels,
                        'dailyData' =>$dailyData,
                        'paymentsTrendsData' => $paymentsTrendsData,
                        'paymentsTrendsLabels' => $paymentsTrendsLabels,
                        'districtLabels' => $districtLabels,
                        'districtData' => $districtData,
                        'paymentStatusData' => $paymentStatusData,
                        'paymentStatusLabels' => $paymentStatusLabels,
                        'paymentStatusColours' => $paymentStatusColours,
                        'paymentTypeLabels' =>$paymentTypeLabels,
                        'paymentTypeData' =>$paymentTypeData 
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
