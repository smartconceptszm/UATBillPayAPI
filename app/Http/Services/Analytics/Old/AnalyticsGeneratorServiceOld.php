<?php

namespace App\Http\Services\Analytics\Old;

use App\Models\DashboardPaymentsProviderTotals;
use App\Models\DashboardRevenueCollectorTotals;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardRevenuePointTotals;
use App\Models\DashboardPaymentTypeTotals;
use App\Http\Services\Auth\UserService;
use App\Models\DashboardHourlyTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnalyticsGeneratorServiceOld
{

   public function __construct(
		private UserService $userService)
	{}

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
         //Step 1 Generate Payments Provider Daily transactions totals
            $paymentsProviderTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('cw.payments_provider_id,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->where('p.created_at', '>=' ,$params['dateFrom'])
                                 ->where('p.created_at', '<=', $params['dateTo'])
                                 ->whereIn('p.paymentStatus', 
                                          [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                             PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                                 ->where('cw.client_id', '=', $params['client_id'])
                                 ->groupBy('cw.payments_provider_id')
                                 ->get();
            if($paymentsProviderTotals->isNotEmpty()){
               $paymentsProviderTotalRecords =[];
               foreach ($paymentsProviderTotals as $paymentsProviderTotal) {
                  $paymentsProviderTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                                      'dateOfTransaction' => $theDate->format('Y-m-d'),'year' => $params['theYear'],
                                                      'payments_provider_id' => $paymentsProviderTotal->payments_provider_id, 
                                                      'numberOfTransactions' => $paymentsProviderTotal->numberOfTransactions,
                                                      'totalAmount'=>$paymentsProviderTotal->totalAmount,];
               }
   
               DashboardPaymentsProviderTotals::upsert(
                        $paymentsProviderTotalRecords,
                        ['client_id','payments_provider_id', 'dateOfTransaction'],
                        ['numberOfTransactions','totalAmount','year','month','day']
                     );
            }
         //
         //Step 2 - Generate RevenuePoint Daily transactions totals
            $revenuePointTotals = DB::table('payments as p')
                                    ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                    ->select(DB::raw('p.revenuePoint,
                                                         COUNT(p.id) AS numberOfTransactions,
                                                            SUM(p.receiptAmount) as totalAmount'))
                                    ->where('p.created_at', '>=' ,$params['dateFrom'])
                                    ->where('p.created_at', '<=', $params['dateTo'])
                                    ->whereIn('p.paymentStatus', 
                                             [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                                PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                                    ->where('cw.client_id', '=', $params['client_id'])
                                    ->groupBy('p.revenuePoint')
                                    ->get();

            $revenuePointTotalRecords =[];
            foreach ($revenuePointTotals as $revenuePointTotal) {
               $revenuePoint = $revenuePointTotal->revenuePoint? $revenuePointTotal->revenuePoint:"OTHER";
               $revenuePointTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                             'numberOfTransactions' => $revenuePointTotal->numberOfTransactions,
                                             'totalAmount'=>$revenuePointTotal->totalAmount, 'year' => $params['theYear'], 
                                             'dateOfTransaction' => $theDate->format('Y-m-d'),'revenuePoint' => $revenuePoint];
            }

            DashboardRevenuePointTotals::upsert(
                     $revenuePointTotalRecords,
                     ['client_id','revenuePoint', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         //
         //Step 3 - Generate Payment Type Daily transactions totals
            $menuTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->join('client_menus as cm','p.menu_id','=','cm.id')
                              ->select(DB::raw('cm.prompt AS paymentType,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->where('p.created_at', '>=' ,$params['dateFrom'])
                              ->where('p.created_at', '<=', $params['dateTo'])
                              ->whereIn('p.paymentStatus', 
                                       [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                          PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->groupBy('cm.prompt')
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
         //Step 4 - Generate Payment Status Daily transactions totals         
            $paymentStatusTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('p.paymentStatus,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->where('p.created_at', '>=' ,$params['dateFrom'])
                              ->where('p.created_at', '<=', $params['dateTo'])
                              ->whereIn('p.paymentStatus', 
                                       [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                          PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->groupBy('p.paymentStatus')
                              ->get();
            $paymentStatusRecords =[];
            foreach ($paymentStatusTotals as $paymentStatusTotal) {
               $paymentStatusRecords[] = ['client_id' => $params['client_id'], 'year' => $params['theYear'], 'month' => $params['theMonth'],
                                          'numberOfTransactions' => $paymentStatusTotal->numberOfTransactions,
                                          'paymentStatus' => $paymentStatusTotal->paymentStatus,'day' => $params['theDay'], 
                                          'dateOfTransaction' => $theDate->format('Y-m-d'),
                                          'totalAmount'=>$paymentStatusTotal->totalAmount];
            }

            $currentEntries = DashboardPaymentStatusTotals::where([
                                                   ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                                                   ['client_id', '=', $params['client_id']],
                                                ])
                                                ->pluck('id')
                                                ->toArray();

            DashboardPaymentStatusTotals::destroy($currentEntries);
            
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
                           ->where('p.created_at', '>=' ,$params['dateFrom'])
                           ->where('p.created_at', '<=', $params['dateTo'])
                           ->whereIn('p.paymentStatus', 
                                    [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                       PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                           ->where('cw.client_id', '=', $params['client_id'])
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
         //Step 6 - Generate Revenue Collector Daily transactions totals
            $revenueCollectorTotals = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->select(DB::raw('p.revenueCollector,
                                          COUNT(p.id) AS numberOfTransactions,
                                             SUM(p.receiptAmount) as totalAmount'))
                     ->where('p.created_at', '>=' ,$params['dateFrom'])
                     ->where('p.created_at', '<=', $params['dateTo'])
                     ->whereIn('p.paymentStatus', 
                              [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                 PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                     ->where('cw.client_id', '=', $params['client_id'])
                     ->groupBy('p.revenueCollector')
                     ->get();

            $revenueCollectorTotalRecords =[];
            foreach ($revenueCollectorTotals as $revenueCollectorTotal) {
               $revenueCollector = "(POS) Point of Sale";
               if($revenueCollectorTotal->revenueCollector){
                  $theUser = $this->userService->findOneBy(['client_id'=>$params['client_id'],
                                                            'revenueCollectorCode'=>$revenueCollectorTotal->revenueCollector]);
                  if($theUser){
                     $revenueCollector = "(".$theUser->revenueCollectorCode.") ".$theUser->fullnames;
                  }
               }
               $revenueCollectorTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                 'numberOfTransactions' => $revenueCollectorTotal->numberOfTransactions,
                                 'totalAmount'=>$revenueCollectorTotal->totalAmount, 'year' => $params['theYear'], 
                                 'dateOfTransaction' => $theDate->format('Y-m-d'),'revenueCollector' => $revenueCollector];
            }

            DashboardRevenueCollectorTotals::upsert(
                        $revenueCollectorTotalRecords,
                        ['client_id','revenueCollector', 'dateOfTransaction'],
                        ['numberOfTransactions','totalAmount','year','month','day']
                     );
         //
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

}
