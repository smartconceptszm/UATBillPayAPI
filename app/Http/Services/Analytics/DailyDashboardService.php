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
         //3. District Totals for Day
            $thePayments = DB::table('dashboard_district_totals as ddt')
                     ->select('ddt.district',
                                       'ddt.numberOfTransactions AS totalTransactions',
                                       'ddt.totalAmount as totalRevenue')
                     ->where('ddt.dateOfTransaction', '=', $dateFrom)
                     ->where('ddt.client_id', '=', $dto->client_id)
                     ->orderBy('ddt.district');
            $byDistrict = $thePayments->get();
            $districtLabels = $byDistrict->map(function ($item) {
                                       return $item->district;
                                    });
            $districtData = $byDistrict->map(function ($item) {
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
         $response = [
                        'paymentsSummary' => $paymentsSummary,
                        'hourlyLabels' => $hourlyLabels,
                        'hourlyData' =>$hourlyData,
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
