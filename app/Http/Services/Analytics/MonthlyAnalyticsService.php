<?php

namespace App\Http\Services\Analytics;

use App\Models\DashboardPaymentsProviderTotals;
use App\Http\Services\Clients\ClientService;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardPaymentTypeTotals;
use App\Models\DashboardDistrictTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MonthlyAnalyticsService
{

   public function __construct(
      private DashboardPaymentsProviderTotals $dashboardPaymentsProviderTotals,
      private ClientService $clientService)
   {}

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         return $this->dashboardPaymentsProviderTotals->where($criteria)->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function generate(Carbon $theDate)
   {
      
      //Process the request
      try {
         $theYear = $theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         $dateFrom = $theYear . '-' . $theMonth . '-01 00:00:00';
         $endOfMonth = $theDate->endOfMonth();
         $theDay = (string)$endOfMonth->day;
         $dateTo = $theYear . '-' . $theMonth . '-' .$theDay. ' 23:59:59';
         Log::info('(SCL) Monthly transaction analytics job launched for: '.$endOfMonth->format('Y-F'));
         $clients = $this->clientService->findAll(['status'=>'ACTIVE']);
         foreach ($clients as $client) {
            //Step 1 - Generate Payment Type Monthly transactions totals
               $menuTotals = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('client_menus as cm','p.menu_id','=','cm.id')
                     ->select(DB::raw('cm.prompt as paymentType,
                                          COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                     ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                     ->where('cw.client_id', '=', $client->id)
                     ->whereIn('p.paymentStatus', 
                              ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                     ->groupBy('paymentType')
                     ->get();
               $menuTotalRecords =[];
               foreach ($menuTotals as  $menuTotal) {
                  $menuTotalRecords[] = ['client_id' => $client->id,'paymentType' => $menuTotal->paymentType, 'year' => $theYear, 
                                          'numberOfTransactions' => $menuTotal->numberOfTransactions,
                                          'month' => $theMonth,'totalAmount'=>$menuTotal->totalAmount];
               }

               DashboardPaymentTypeTotals::upsert(
                                                $menuTotalRecords,
                                                ['client_id','year','month', 'paymentType'],
                                                ['numberOfTransactions','totalAmount']
                                             );
            //
            //Step 2 - Generate Payment Status Monthly transactions totals
               $paymentStatusTotals = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->select(DB::raw('p.paymentStatus,
                                          COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                     ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                     ->where('cw.client_id', '=', $client->id)
                     ->whereIn('p.paymentStatus', 
                              ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                     ->groupBy('paymentStatus')
                     ->get();
               $paymentStatusRecords =[];
               foreach ($paymentStatusTotals as  $paymentStatusTotal) {
                  $paymentStatusRecords[] = ['client_id' => $client->id, 'year' => $theYear, 'month' => $theMonth,
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
            //Step 3 - Generate District Monthly transactions totals
               $districtTotals = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->select(DB::raw('p.district,
                                          COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                     ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                     ->where('cw.client_id', '=', $client->id)
                     ->whereIn('p.paymentStatus', 
                              ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                     ->groupBy('p.district')
                     ->get();
               $districtTotalRecords =[];
               foreach ($districtTotals as $districtTotal) {
                  $district = $districtTotal->district? $districtTotal->district:"OTHER";
                  $districtTotalRecords[] = ['client_id' => $client->id,'district' => $district, 'year' => $theYear, 
                                                'numberOfTransactions' => $districtTotal->numberOfTransactions,
                                                'month' => $theMonth,'totalAmount'=>$districtTotal->totalAmount];
               }

               DashboardDistrictTotals::upsert(
                                          $districtTotalRecords,
                                          ['client_id','year','month','district'],
                                          ['numberOfTransactions','totalAmount']
                                       );
            //
            //Step 4 - Generate Payments Provider Monthly transactions totals
               $paymentProviderTotals = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->select(DB::raw('cw.payments_provider_id,
                                          COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                     ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                     ->where('cw.client_id', '=', $client->id)
                     ->whereIn('p.paymentStatus', 
                              ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                     ->groupBy('payments_provider_id')
                     ->get();           
               $paymentProviderRecords =[];
               foreach ($paymentProviderTotals as $key => $paymentProviderTotal) {
                  $paymentProviderRecords[] = ['client_id' => $client->id,'year' => $theYear, 'month' => $theMonth,
                                          'payments_provider_id' => $paymentProviderTotal->payments_provider_id, 
                                          'numberOfTransactions' => $paymentProviderTotal->numberOfTransactions,
                                          'totalAmount'=>$paymentProviderTotal->totalAmount];
               }

               DashboardPaymentsProviderTotals::upsert(
                                                $paymentProviderRecords,
                                                ['client_id', 'year','month','payments_provider_id'],
                                                ['numberOfTransactions','totalAmount']
                                             );
            //
         }
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      Log::info('(SCL) Monthly transaction analytics job executed for: '.$theDate->format('Y-F'));
      return true;
      
   }

}
