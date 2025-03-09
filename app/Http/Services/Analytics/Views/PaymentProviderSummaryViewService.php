<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentProviderSummaryViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;

         $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                           ->select(DB::raw('p.shortName as paymentsProvider,p.colour,
                                                SUM(ppt.numberOfTransactions) AS totalTransactions,
                                                   SUM(ppt.totalAmount) as totalRevenue'))
                           ->whereBetween('ppt.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('ppt.client_id', '=', $dto->client_id)
                           ->groupBy('paymentsProvider','colour')
                           ->orderByDesc('totalRevenue');
         // $theSQLQuery = $thePayments->toSql();
         // $theBindings = $thePayments-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $byPaymentProvider = $thePayments->get();

         $theLabels = $byPaymentProvider->pluck('paymentsProvider')->unique()->values();
         $theLabels->prepend('TOTAL');
         $theData = $byPaymentProvider->map(function ($item) {
                                          return [
                                             'label'=>$item->paymentsProvider.
                                                         ' ('.number_format($item->totalTransactions,0,'.',',').')',
                                             'data'=>$item->totalRevenue,
                                             'labelColour'=>$item->paymentsProvider,
                                             'colour'=>$item->colour
                                          ];
                                       });

         $theData->prepend([  
                        'label'=> 'TOTAL ('.number_format($byPaymentProvider->sum('totalTransactions'),0,'.',',') .')',
                        'data'=>$byPaymentProvider->sum('totalRevenue'),
                        'labelColour'=>"TOTAL",
                        'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                     ]);
   
         return [
                  'labels' => $theLabels,
                  'datasets' => $theData
               ];

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
