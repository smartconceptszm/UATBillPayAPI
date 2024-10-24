<?php

namespace App\Http\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ClientMonthlyDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m',$dto->theMonth);
         $theYear = (string)$theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         //1. Payments Provider Totals for Month
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select('ppt.*','p.shortName as paymentsProvider','p.colour')
                              ->where('ppt.year', '=',  $theYear)
                              ->where('ppt.month', '=',  $theMonth)
                              ->where('ppt.client_id', '=', $dto->client_id)
                              ->orderByDesc('totalAmount');
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $byPaymentProvider = $thePayments->get();
            $totalRevenue = $byPaymentProvider->reduce(function ($totalRevenue, $item) {
                                                return $totalRevenue + $item->totalAmount;
                                          });
            $paymentsSummary = $byPaymentProvider->map(function ($item) {
                                                      return [
                                                         'paymentsProvider'=>$item->paymentsProvider,
                                                         'totalAmount'=>$item->totalAmount,
                                                         'colour'=>$item->colour
                                                      ];
                                                });
            $paymentsSummary->prepend([
                                       'paymentsProvider'=>'TOTAL',
                                       'totalAmount'=> $totalRevenue,
                                       'colour'=>$billpaySettings['ANALYTICS_PROVIDER_TOTALS_COLOUR']
                                 ]);


         //
         //2. District Totals for Month
            $thePayments = DB::table('dashboard_district_totals as ddt')
                                 ->select('ddt.*')
                                 ->where('ddt.year', '=',  $theYear)
                                 ->where('ddt.month', '=',  $theMonth)
                                 ->where('ddt.client_id', '=', $dto->client_id);
            $byDistrict = $thePayments->get();
            $districtLabels = $byDistrict->map(function ($item) {
                                                   return $item->district;
                                             });
            $districtData = $byDistrict->map(function ($item) {
                                                return $item->totalAmount;
                                          });
         //
         //3. Payment Type Totals for Month
            $thePayments = DB::table('dashboard_payment_type_totals as menuTotals')
                     ->select('menuTotals.*')
                     ->where('menuTotals.year', '=',  $theYear)
                     ->where('menuTotals.month', '=',  $theMonth)
                     ->where('menuTotals.client_id', '=', $dto->client_id);
            $menuTotals = $thePayments->get();
            $paymentTypeLabels = $menuTotals->map(function ($item) {
                                                return $item->paymentType."(".$item->numberOfTransactions.")";
                                          });
            $paymentTypeData = $menuTotals->map(function ($item) {
                                             return $item->totalAmount;
                                       });
         //
         //4. Payments Status Totals for Month
            $thePayments = DB::table('dashboard_payment_status_totals as pst')
                              ->select('pst.*')
                              ->where('pst.year', '=',  $theYear)
                              ->where('pst.month', '=',  $theMonth)
                              ->where('pst.client_id', '=', $dto->client_id)
                              ->orderBy('paymentStatus');
            $thePayments = $thePayments->get();

            $paymentStatusData = $thePayments->map(function ($item) {
                                                      return $item->totalAmount;
                                                });
            $paymentStatusLabels = $thePayments->map(function ($item) {
                                                   return $item->paymentStatus."(".$item->numberOfTransactions.")";
                                             });
            $paymentStatusColours = $thePayments->map(function ($item) use($billpaySettings) {
                                                return $billpaySettings[$item->paymentStatus.'_COLOUR'];
                                          });        
         //
         $response = [
                        'paymentsSummary' => $paymentsSummary,
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
