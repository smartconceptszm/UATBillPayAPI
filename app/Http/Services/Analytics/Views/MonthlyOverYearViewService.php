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

         $endOfMonth = $dto->dateTo->copy()->endOfMonth();

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
                                          ->orderBy('month')
                                          ->get();

         $paymentsProviders = $paymentsTrends->mapWithKeys(fn($item) => [$item->paymentsProvider => $item->colour])->all();
         $paymentsProviders['TOTAL'] = $billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR'];
         $paymentsProviders = collect($paymentsProviders);

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

         $monthylPaymentProviderTotals = $paymentsProviders->map(function ($colour, $paymentsProvider) use ($paymentsTrends, $monthlyTotals, $monthsCollection){
                                                   
                                                   if($paymentsProvider == 'TOTAL'){
                                                      return collect([
                                                               'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                                               'borderColor' => $colour,
                                                               'pointBackgroundColor' => $colour,
                                                               'pointBorderColor' => $colour,
                                                               'label' => $paymentsProvider,
                                                               'data' => $monthlyTotals
                                                            ]);
                                                   }else{
                                                      $theData = $monthsCollection->map(function ($monthInFocus) use ($paymentsTrends, $paymentsProvider){
                                                                        $providerRecordForMonthInFocus = $paymentsTrends->first(function ($record) use($monthInFocus,$paymentsProvider) {
                                                                                                return (($record->paymentsProvider == $paymentsProvider)
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
                                                      return collect([
                                                               'backgroundColor'=> "rgba(255, 255, 255,0.2)",
                                                               'borderColor' => $colour,
                                                               'pointBackgroundColor' => $colour,
                                                               'pointBorderColor' => $colour,
                                                               'label' => $paymentsProvider,
                                                               'data' => $theData
                                                            ]);
                                                   }
                                                });
                                                
         $paymentsTrendsLabels  = $monthsCollection->map(function ($record){
                                          $theDate = Carbon::createFromFormat('Y-m-d',$record['year'].'-'.$record['month'].'-01');
                                          return $theDate->format('M Y');
                                       });


         $datasets =  $monthylPaymentProviderTotals->map(fn($item) => collect($item))->values()->all();                              
         $response =[];
         $response['datasets'] = $datasets;
         $response['labels'] = $paymentsTrendsLabels;

         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }



}
