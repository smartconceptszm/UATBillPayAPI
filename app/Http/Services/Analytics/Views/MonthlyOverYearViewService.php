<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MonthlyOverYearViewService
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

         $endOfMonth = $dateTo->copy()->endOfMonth();

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
         $response = [
                     'paymentsTrendsData' => $paymentsTrendsData,
                     'paymentsTrendsLabels' => $paymentsTrendsLabels,
                  ];
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }



}
