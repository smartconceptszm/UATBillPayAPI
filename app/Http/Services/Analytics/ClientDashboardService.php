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
         $dateFrom = $dateFrom->copy()->startOfDay();
         $dateTo = Carbon::parse($dto->dateTo);
         $dateTo = $dateTo->copy()->endOfDay();

         $endOfMonth = $dateTo->copy()->endOfMonth();
         $theYear = (string)$endOfMonth->year;
         $theMonth = $endOfMonth->month;

         //1. Payments Provider Totals for Month
            $thePayments = DB::table('dashboard_daily_totals as ddt')
                              ->join('payments_providers as p','ddt.payments_provider_id','=','p.id')
                              ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                   SUM(ddt.numberOfTransactions) AS totalTransactions,
                                                      SUM(ddt.totalAmount) as totalRevenue'))
                              ->whereBetween('ddt.dateOfTransaction', [$dateFrom, $dateTo])
                              ->where('ddt.client_id', '=', $dto->client_id)
                              ->groupBy('paymentsProvider')
                              // ->groupBy('colour')
                              ->orderByDesc('totalRevenue');
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $byPaymentProvider = $thePayments->get();
            $totalRevenue = $byPaymentProvider->reduce(function ($totalRevenue, $item) {
                                                return $totalRevenue + $item->totalRevenue;
                                          });
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
            $thePayments = DB::table('dashboard_daily_totals as ddt')
                              ->select('dt.*')
                              ->select(DB::raw('ddt.day,
                                                SUM(ddt.numberOfTransactions) AS totalTransactions,
                                                SUM(ddt.totalAmount) as totalRevenue'))
                              ->where('ddt.year', '=',  $theYear)
                              ->where('ddt.month', '=',  $theMonth)
                              ->where('ddt.client_id', '=', $dto->client_id)
                              ->groupBy('day')
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
                              ->select('ppt.*','p.shortName as paymentsProvider','p.colour')
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
            $paymentsTrends = $paymentsTrends//->orderBy('paymentsProvider')
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
                                       return $recordsForMonth->reduce(function ($totalRevenue, $item) {
                                                                        return $totalRevenue + $item->totalAmount;
                                                                  });
                                    }else{
                                       return 0;
                                    }
                                 });

            $paymentsTrendsLabels  = $monthsCollection->map(function ($record){
                                             $theDate = Carbon::createFromFormat('Y-m',$record['year'].'-'.$record['month']);
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
                                                               return $providerRecordForMonthInFocus->totalAmount;
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
         $response = [
                        'paymentsSummary' => $paymentsSummary,
                        'dailyLabels' => $dailyLabels,
                        'dailyData' =>$dailyData,
                        'paymentsTrendsData' => $paymentsTrendsData,
                        'paymentsTrendsLabels' => $paymentsTrendsLabels,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
