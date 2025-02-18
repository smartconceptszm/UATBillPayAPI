<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentProviderSummaryViewService
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

         $response = [
                        'paymentsSummary' => $paymentsSummary,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
