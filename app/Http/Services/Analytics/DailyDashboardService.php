<?php

namespace App\Http\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class DailyDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;
         $theDate = Carbon::parse($dto->theDate);
         $dateFrom = $theDate->format('Y-m-d');

         //1. Payments Provider Totals for Day
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select('p.shortName as paymentsProvider','p.colour',
                                          'ppt.numberOfTransactions AS totalTransactions',
                                             'ppt.totalAmount as totalRevenue')
                              ->where('ppt.dateOfTransaction', '=', $dateFrom)
                              ->where('ppt.client_id', '=', $dto->client_id)
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
         //2. Hourly Totals over the Day
            $hourlyTrends = DB::table('dashboard_hourly_totals as dht')
                           ->select('dht.hour','dht.numberOfTransactions',
                                       'dht.totalAmount')
                           ->where('dht.dateOfTransaction', '=', $dateFrom)
                           ->where('dht.client_id', '=', $dto->client_id)
                           ->orderBy('dht.hour');
            $hourlyTrends = $hourlyTrends->get();
            $hourlyLabels = $hourlyTrends->map(function ($item) {
                                             return $item->hour;
                                          });
            $hourlyData = $hourlyTrends->map(function ($item) {
                                             return $item->totalAmount;
                                          });
         //
         //3. RevenuePoint Totals for Day
            $thePayments = DB::table('dashboard_revenue_point_totals as drpt')
                     ->select('drpt.revenuePoint',
                                       'drpt.numberOfTransactions AS totalTransactions',
                                       'drpt.totalAmount as totalRevenue')
                     ->where('drpt.dateOfTransaction', '=', $dateFrom)
                     ->where('drpt.client_id', '=', $dto->client_id)
                     ->orderBy('drpt.revenuePoint');
            $byRevenuePoint = $thePayments->get();
            $revenuePointLabels = $byRevenuePoint->map(function ($item) {
                                       return $item->revenuePoint;
                                    });
            $revenuePointData = $byRevenuePoint->map(function ($item) {
                                    return $item->totalRevenue;
                                 });
         //
         //4. Payment Type Totals for Day
            $thePayments = DB::table('dashboard_payment_type_totals as ptt')
                                 ->select('ptt.paymentType',
                                             'ptt.numberOfTransactions AS totalTransactions',
                                              'ptt.totalAmount as totalRevenue')
                                 ->where('ptt.dateOfTransaction', '=', $dateFrom)
                                 ->where('ptt.client_id', '=', $dto->client_id)
                                 ->orderBy('paymentType');
            $menuTotals = $thePayments->get();
            $paymentTypeLabels = $menuTotals->map(function ($item) {
                                       return $item->paymentType."(".$item->totalTransactions.")";
                                    });
            $paymentTypeData = $menuTotals->map(function ($item) {
                                       return $item->totalRevenue;
                                    });
         //
         //5. Payments Status Totals for Day
            $thePayments = DB::table('dashboard_payment_status_totals as pst')
                                    ->select('pst.paymentStatus',
                                                'pst.numberOfTransactions AS totalTransactions',
                                                'pst.totalAmount as totalRevenue')
                                    ->where('pst.dateOfTransaction', '=', $dateFrom)
                                    ->where('pst.client_id', '=', $dto->client_id)
                                    ->orderBy('pst.paymentStatus');
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
         //6. Revenue Collector Totals for Day
            $thePayments = DB::table('dashboard_revenue_collector_totals as drct')
                     ->select('drct.revenueCollector',
                                       'drct.numberOfTransactions AS totalTransactions',
                                       'drct.totalAmount as totalRevenue')
                     ->where('drct.dateOfTransaction', '=', $dateFrom)
                     ->where('drct.client_id', '=', $dto->client_id)
                     ->orderBy('drct.revenueCollector');
            $byRevenueCollector = $thePayments->get();
            $revenueCollectorLabels = $byRevenueCollector->map(function ($item) {
                                       return $item->revenueCollector;
                                    });
            $revenueCollectorData = $byRevenueCollector->map(function ($item) {
                                    return $item->totalRevenue;
                                 });
         //
         $response = [
                        'paymentsSummary' => $paymentsSummary,
                        'hourlyLabels' => $hourlyLabels,
                        'hourlyData' =>$hourlyData,
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
