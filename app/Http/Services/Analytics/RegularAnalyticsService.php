<?php

namespace App\Http\Services\Analytics;

use App\Models\DashboardPaymentsProviderTotals;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardPaymentTypeTotals;
use App\Models\DashboardDistrictTotals;
use App\Models\DashboardDailyTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class RegularAnalyticsService
{

   public function generate(BaseDTO $paymentDTO)
   {
      
      //Process the request
      try {
         $dateFrom = \substr($paymentDTO->created_at,0,10)." 00:00:00";
         $dateTo = \substr($paymentDTO->created_at,0,10)." 23:59:59";

         $theDate = Carbon::parse($paymentDTO->created_at);
         $theYear = $theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         $startOfMonth = $theYear . '-' . $theMonth . '-01 00:00:00';

         //Step 1 Get Daily transactions totals
            $dailyTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('dayofmonth(p.created_at) as day,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                                 ->where('cw.client_id', '=', $paymentDTO->client_id)
                                 ->whereIn('p.paymentStatus', 
                                          ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                 ->groupBy('day')
                                 ->get();
            if($dailyTotals->isNotEmpty()){
               $dailyTotals =$dailyTotals[0];
               DashboardDailyTotals::upsert(
                        [
                           ['client_id' => $paymentDTO->client_id, 'year' => $theYear, 'month' => $theMonth, 'day' => $dailyTotals->day,
                              'numberOfTransactions' => $dailyTotals->numberOfTransactions,'totalAmount'=>$dailyTotals->totalAmount]
                        ],
                        ['client_id','year','month','day'],
                        ['numberOfTransactions','totalAmount']
                     );
            }
         //
         //Step 2 - Get Payment Type Monthly transactions totals
            $menuTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->join('client_menus as cm','p.menu_id','=','cm.id')
                              ->select(DB::raw('cm.prompt as paymentType,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$startOfMonth, $dateTo])
                              ->where('cw.client_id', '=', $paymentDTO->client_id)
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('paymentType')
                              ->get();
            $menuTotalRecords =[];
            foreach ($menuTotals as  $menuTotal) {
               $menuTotalRecords[] = ['client_id' => $paymentDTO->client_id,'paymentType' => $menuTotal->paymentType, 
                                       'year' => $theYear, 'numberOfTransactions' => $menuTotal->numberOfTransactions,
                                       'month' => $theMonth,'totalAmount'=>$menuTotal->totalAmount];
            }

            DashboardPaymentTypeTotals::upsert(
                     $menuTotalRecords,
                     ['client_id','year','month', 'paymentType'],
                     ['numberOfTransactions','totalAmount']
               );
         //
         //Step 3 - Get Payment Status Monthly transactions totals
            $paymentStatusTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->join('client_menus as cm','p.menu_id','=','cm.id')
                              ->select(DB::raw('p.paymentStatus,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$startOfMonth, $dateTo])
                              ->where('cw.client_id', '=', $paymentDTO->client_id)
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('paymentStatus')
                              ->get();
            $paymentStatusRecords =[];
            foreach ($paymentStatusTotals as $paymentStatusTotal) {
               $paymentStatusRecords[] = ['client_id' => $paymentDTO->client_id, 'year' => $theYear, 'month' => $theMonth,
                                             'numberOfTransactions' => $paymentStatusTotal->numberOfTransactions,
                                             'paymentStatus' => $paymentStatusTotal->paymentStatus, 
                                             'totalAmount'=>$paymentStatusTotal->totalAmount];
            }

            DashboardPaymentStatusTotals::upsert(
                     $paymentStatusRecords,
                     ['client_id','year','month', 'paymentStatus'],
                     ['numberOfTransactions','totalAmount']
               );
         //
         //Step 4 - Get District Monthly transactions totals
            $districtTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('p.district,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$startOfMonth, $dateTo])
                              ->where('cw.client_id', '=', $paymentDTO->client_id)
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('p.district')
                              ->get();
            $districtTotalRecords =[];
            foreach ($districtTotals as $districtTotal) {
               $district = $districtTotal->district? $districtTotal->district:"OTHER";
               $districtTotalRecords[] = ['client_id' => $paymentDTO->client_id,'district' => $district, 'year' => $theYear, 
                                             'numberOfTransactions' => $districtTotal->numberOfTransactions,
                                             'month' => $theMonth,'totalAmount'=>$districtTotal->totalAmount];
            }

            DashboardDistrictTotals::upsert(
                     $districtTotalRecords,
                     ['client_id','year','month','district'],
                     ['numberOfTransactions','totalAmount']
               );
         //
         //Step 5 - Get Payments Provider Monthly transactions totals
            $paymentProviderTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('cw.payments_provider_id,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$startOfMonth, $dateTo])
                              ->where('cw.client_id', '=', $paymentDTO->client_id)
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('payments_provider_id')
                              ->get();           
            $paymentProviderRecords =[];
            foreach ($paymentProviderTotals as $paymentProviderTotal) {
               $paymentProviderRecords[] = ['client_id' => $paymentDTO->client_id,'year' => $theYear, 'month' => $theMonth,
                                                'numberOfTransactions' => $paymentProviderTotal->numberOfTransactions,
                                                'paymentsProvider' => $paymentProviderTotal->payments_provider_id, 
                                                'totalAmount'=>$paymentProviderTotal->totalAmount];
            }

            DashboardPaymentsProviderTotals::upsert(
                     $paymentProviderRecords,
                     ['client_id', 'year','month','payments_provider_id'],
                     ['numberOfTransactions','totalAmount']
               );
         //
      } catch (\Throwable $e) {
         $paymentDTO->error='At . '.$e->getMessage();
         Log::info($paymentDTO->error);
         return false;
      }
      return true;
      
   }

}
