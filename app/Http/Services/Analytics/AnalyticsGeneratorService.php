<?php

namespace App\Http\Services\Analytics;

use App\Models\DashboardPaymentsProviderTotals;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardPaymentTypeTotals;
use App\Models\DashboardDistrictTotals;
use App\Models\DashboardHourlyTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class AnalyticsGeneratorService
{

   public function generate(array $params)
   {
      
      try {
         
         $theDate = $params['theDate'];
         //Step 1 Generate Payments Provider transactions totals
            $paymentsProviderTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('cw.payments_provider_id,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->whereBetween('p.created_at', [$params['dateFrom'], $params['dateTo']])
                                 ->where('cw.client_id', '=', $params['client_id'])
                                 ->whereIn('p.paymentStatus', 
                                          ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                 ->groupBy('payments_provider_id')
                                 ->get();
            if($paymentsProviderTotals->isNotEmpty()){
               $paymentsProviderTotalRecords =[];
               foreach ($paymentsProviderTotals as $paymentsProviderTotal) {
                  $paymentsProviderTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                      'payments_provider_id' => $paymentsProviderTotal->payments_provider_id, 
                                                      'numberOfTransactions' => $paymentsProviderTotal->numberOfTransactions,
                                                      'totalAmount'=>$paymentsProviderTotal->totalAmount, 'year' => $params['theYear'], 
                                                      'dateOfTransaction' => $theDate->format('Y-m-d')];
               }
   
               DashboardPaymentsProviderTotals::upsert(
                        $paymentsProviderTotalRecords,
                        ['client_id','payments_provider_id', 'dateOfTransaction'],
                        ['numberOfTransactions','totalAmount','year','month','day']
                     );
            }
         //
         //Step 2 - Generate District Monthly transactions totals
            $districtTotals = DB::table('payments as p')
                                    ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                    ->select(DB::raw('p.district,
                                                         COUNT(p.id) AS numberOfTransactions,
                                                            SUM(p.receiptAmount) as totalAmount'))
                                    ->whereBetween('p.created_at', [$params['dateFrom'], $params['dateTo']])
                                    ->where('cw.client_id', '=', $params['client_id'])
                                    ->whereIn('p.paymentStatus', 
                                             ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                    ->groupBy('p.district')
                                    ->get();

            $districtTotalRecords =[];
            foreach ($districtTotals as $districtTotal) {
               $district = $districtTotal->district? $districtTotal->district:"OTHER";
               $districtTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                             'numberOfTransactions' => $districtTotal->numberOfTransactions,
                                             'totalAmount'=>$districtTotal->totalAmount, 'year' => $params['theYear'], 
                                             'dateOfTransaction' => $theDate->format('Y-m-d'),'district' => $district];
            }

            DashboardDistrictTotals::upsert(
                     $districtTotalRecords,
                     ['client_id','district', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         //
         //Step 3 - Generate Payment Type Monthly transactions totals
            $menuTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->join('client_menus as cm','p.menu_id','=','cm.id')
                              ->select(DB::raw('cm.prompt as paymentType,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$params['dateFrom'], $params['dateTo']])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('paymentType')
                              ->get();
            $menuTotalRecords =[];
            foreach ($menuTotals as  $menuTotal) {
               $menuTotalRecords[] = ['client_id' => $params['client_id'],'paymentType' => $menuTotal->paymentType, 
                                       'year' => $params['theYear'], 'numberOfTransactions' => $menuTotal->numberOfTransactions
                                       ,'month' => $params['theMonth'],'dateOfTransaction' => $theDate->format('Y-m-d'),
                                       'day' => $params['theDay'],'totalAmount'=>$menuTotal->totalAmount];
            }

            DashboardPaymentTypeTotals::upsert(
                     $menuTotalRecords,
                     ['client_id','paymentType', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
               );
         //
         //Step 4 - Generate Payment Status Monthly transactions totals
            $paymentStatusTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('p.paymentStatus,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at', [$params['dateFrom'], $params['dateTo']])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->whereIn('p.paymentStatus', 
                                       ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                              ->groupBy('paymentStatus')
                              ->get();
            $paymentStatusRecords =[];
            foreach ($paymentStatusTotals as $paymentStatusTotal) {
               $paymentStatusRecords[] = ['client_id' => $params['client_id'], 'year' => $params['theYear'], 'month' => $params['theMonth'],
                                          'numberOfTransactions' => $paymentStatusTotal->numberOfTransactions,
                                          'paymentStatus' => $paymentStatusTotal->paymentStatus,'day' => $params['theDay'], 
                                          'dateOfTransaction' => $theDate->format('Y-m-d'),
                                          'totalAmount'=>$paymentStatusTotal->totalAmount];
            }

            DashboardPaymentStatusTotals::upsert(
                     $paymentStatusRecords,
                     ['client_id','paymentStatus', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         //
         //Step 5 - Generate Hourly transactions totals
            $hourlyTotals = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->select(DB::raw('HOUR(p.created_at) AS hour,
                                                COUNT(p.id) AS numberOfTransactions,
                                                   SUM(p.receiptAmount) as totalAmount'))
                           ->whereBetween('p.created_at', [$params['dateFrom'], $params['dateTo']])
                           ->where('cw.client_id', '=', $params['client_id'])
                           ->whereIn('p.paymentStatus', 
                                    ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                           ->groupBy('hour')
                           ->orderBy('hour')
                           ->get();
            if($hourlyTotals->isNotEmpty()){
               $hourlTotalRecords =[];
               foreach ($hourlyTotals as $hourlTotal) {
                  $hourlTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                   'numberOfTransactions' => $hourlTotal->numberOfTransactions,
                                                   'totalAmount'=>$hourlTotal->totalAmount, 'year' => $params['theYear'], 
                                                   'dateOfTransaction' => $theDate->format('Y-m-d'),
                                                   'hour' => $hourlTotal->hour
                                                ];
               }

               DashboardHourlyTotals::upsert(
                     $hourlTotalRecords,
                     ['client_id','payments_provider_id', 'dateOfTransaction','hour'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
            }
         //

      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      return true;
      
   }

}
