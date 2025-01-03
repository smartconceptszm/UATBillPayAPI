<?php

namespace App\Http\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MainDashboardService 
{

   public function findAll(array $criteria)
   {
      
      try {
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m-d',$dto->theMonth.'-01');
         $theYear = (string)$theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         //Get all in Date Range
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->join('clients as c','ppt.client_id','=','c.id')
                           ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                           ->select(DB::raw('c.id,c.urlPrefix,c.name, p.shortName as paymentsProvider,p.colour,
                                                SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                   SUM(ppt.totalAmount) as totalRevenue'))
                           ->where('ppt.year', '=',  $theYear)
                           ->where('ppt.month', '=',  $theMonth)
                           ->groupBy('c.id','c.urlPrefix','c.name')
                           ->groupBy('paymentsProvider','p.colour')
                           ->orderBy('totalRevenue','desc')
                           ->get();
            if($thePayments->isNotEmpty()){
               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
               $paymentsByClient = $thePayments->groupBy('urlPrefix');
               $paymentsSummary = $paymentsByClient->map(function ($client) use ($billpaySettings) {

                                    $clientDetails = $client->get(0);
                                    
                                    $formattedData = $client->map(function ($item) {
                                                   return [
                                                      'paymentsProvider'=>$item->paymentsProvider,
                                                      'totalTransactions'=>$item->totalTransactions,
                                                      'totalAmount'=>$item->totalRevenue,
                                                      'colour'=>$item->colour
                                                   ];
                                                });
                                    $totalRevenue = $client->reduce(function ($totalRevenue, $item) {
                                                   return $totalRevenue + $item->totalRevenue;
                                             }); 
                                    $totalTransactions = $client->reduce(function ($transactions, $item) {
                                                   return $transactions + $item->totalTransactions;
                                             }); 
                                    $formattedData->prepend([
                                             'paymentsProvider'=>'TOTAL',
                                             'totalTransactions'=>$totalTransactions,
                                             'totalAmount'=> $totalRevenue,
                                             'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                                       ]);
                                    
                                    return [
                                             'urlPrefix'=>$clientDetails->urlPrefix,
                                             'name'=>$clientDetails->name,
                                             'id'=>$clientDetails->id,
                                             'totalRevenue' =>  $totalRevenue,
                                             'data'=>$formattedData->toArray()
                                          ];
                                 });

               $paymentsSummary = $paymentsSummary->sortByDesc('totalRevenue',SORT_NUMERIC);
               return $paymentsSummary;
            }else{
               return [];
            }
         //
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
