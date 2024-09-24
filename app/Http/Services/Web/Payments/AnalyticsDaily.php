<?php

namespace App\Http\Services\Web\Payments;

use App\Models\DashboardDailyTotals;
use App\Models\DashboardMonthlyTrends;
use App\Models\DashboardDistrictTotals;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardPaymentProviderTotals;
use App\Models\DashboardPaymentType;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\DTOs\BaseDTO;

class AnalyticsDaily
{

   public function handle(BaseDTO $paymentDTO)
   {
      
      //Process the request
      try {
         
      
      $dateFrom = \substr($paymentDTO->created_at,0,10)." 00:00:00";
      $dateTo = \substr($paymentDTO->created_at,0,10)." 23:59:59";

      //Step 1 Get Daily transactions totals
      $dailyTotals = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                                COUNT(p.id) AS noOfPayments,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                           ->where('cw.client_id', '=', $paymentDTO->client_id)
                           ->whereIn('p.paymentStatus', 
                                    ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                           ->groupBy('dayOfTx')
                           ->orderBy('dayOfTx')
                           ->get();

      DB::table('dashboard_daily_totals')->upsert(
                              [
                                  ['departure' => 'Oakland', 'destination' => 'San Diego', 'price' => 99],
                                  ['departure' => 'Chicago', 'destination' => 'New York', 'price' => 150]
                              ],
                              ['departure', 'destination'],
                              ['price']
                          );
      //Step 2 - Get Daily transactions totals by Menu
      $menuTotals = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                             cm.prompt as paymentType,
                                             COUNT(p.id) AS noOfPayments,
                                                SUM(p.receiptAmount) as totalAmount'))
                        ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                        ->where('cw.client_id', '=', $paymentDTO->client_id)
                        ->whereIn('p.paymentStatus', 
                                 ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                        ->groupBy('dayOfTx')
                        ->groupBy('paymentType')
                        ->orderBy('dayOfTx')
                        ->get();

      //Step 2 - Get Daily transactions totals by Payment Status
      $paymentStatusTotals = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                             p.paymentStatus,
                                             COUNT(p.id) AS noOfPayments,
                                                SUM(p.receiptAmount) as totalAmount'))
                        ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                        ->where('cw.client_id', '=', $paymentDTO->client_id)
                        ->whereIn('p.paymentStatus', 
                                 ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                        ->groupBy('dayOfTx')
                        ->groupBy('paymentStatus')
                        ->orderBy('dayOfTx')
                        ->get();

      //Step 2 - Get Daily transactions totals by District
      $districtTotals = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                             p.district,
                                             COUNT(p.id) AS noOfPayments,
                                                SUM(p.receiptAmount) as totalAmount'))
                        ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                        ->where('cw.client_id', '=', $paymentDTO->client_id)
                        ->whereIn('p.paymentStatus', 
                                 ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                        ->groupBy('dayOfTx')
                        ->groupBy('district')
                        ->orderBy('dayOfTx')
                        ->get();

      //Step 2 - Get Daily transactions totals by Payment Provider
      $paymentProviderTotals = DB::table('payments as p')
                           ->join('client_menus as cm','p.menu_id','=','cm.id')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                           ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                                pp.shortName as paymentProvider,
                                                COUNT(p.id) AS noOfPayments,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                           ->where('cw.client_id', '=', $paymentDTO->client_id)
                           ->whereIn('p.paymentStatus', 
                                    ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                           ->groupBy('dayOfTx')
                           ->groupBy('paymentProvider')
                           ->orderBy('dayOfTx')
                           ->get();           

      } catch (\Throwable $e) {
         $paymentDTO->error='At get initiate payment pipeline. '.$e->getMessage();
         Log::info($paymentDTO->error);
      }

      return $paymentDTO;
      
   }

}
