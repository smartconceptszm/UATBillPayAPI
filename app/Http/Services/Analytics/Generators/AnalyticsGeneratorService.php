<?php

namespace App\Http\Services\Analytics\Generators;


use App\Models\DashboardPaymentsProviderTotals;
use App\Models\DashboardRevenueCollectorTotals;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardPaymentStatusTotals;
use App\Models\DashboardRevenuePointTotals;
use App\Models\DashboardConsumerTierTotals;
use App\Models\DashboardConsumerTypeTotals;
use App\Models\DashboardPaymentTypeTotals;
use App\Models\DashboardHourlyTotals;
use App\Http\Services\Auth\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AnalyticsGeneratorService
{

   public function __construct(
		private UserService $userService)
	{}

   public function generate(array $params)
   {
      
      try {

         $theDate = $params['theDate'];
         
         //Step 1 Generate Consumer Tier Daily transactions totals
            $consumerTierTotals = DB::table('payments as p')
                                    ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                    ->select(DB::raw('p.consumerTier,
                                                         COUNT(p.id) AS numberOfTransactions,
                                                            SUM(p.receiptAmount) as totalAmount'))
                                    ->where('p.created_at', '>=' ,$params['dateFrom'])
                                    ->where('p.created_at', '<=', $params['dateTo'])
                                    ->whereIn('p.paymentStatus', 
                                             [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                                PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                                    ->where('cw.client_id', '=', $params['client_id'])
                                    ->groupBy('p.consumerTier')
                                    ->get();

            $consumerTierTotalRecords =[];
            foreach ($consumerTierTotals as $consumerTierTotal) {
               $consumerTier = $consumerTierTotal->consumerTier? $consumerTierTotal->consumerTier:"OTHER";
               $consumerTierTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                             'numberOfTransactions' => $consumerTierTotal->numberOfTransactions,
                                             'totalAmount'=>$consumerTierTotal->totalAmount, 'year' => $params['theYear'], 
                                             'dateOfTransaction' => $theDate->format('Y-m-d'),'consumerTier' => $consumerTier];
            }

            $currentEntries = DashboardConsumerTierTotals::where([
                                          ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                                          ['client_id', '=', $params['client_id']],
                                       ])
                                       ->pluck('id')
                                       ->toArray();

            DashboardConsumerTierTotals::destroy($currentEntries);


            DashboardConsumerTierTotals::upsert(
                     $consumerTierTotalRecords,
                     ['client_id','consumerTier', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         //

         //Step 2 Generate Consumer Type Daily transactions totals
            $consumerTypeTotals = DB::table('payments as p')
                                    ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                    ->select(DB::raw('p.consumerType,
                                                         COUNT(p.id) AS numberOfTransactions,
                                                            SUM(p.receiptAmount) as totalAmount'))
                                    ->where('p.created_at', '>=' ,$params['dateFrom'])
                                    ->where('p.created_at', '<=', $params['dateTo'])
                                    ->whereIn('p.paymentStatus', 
                                             [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                                PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                                    ->where('cw.client_id', '=', $params['client_id'])
                                    ->groupBy('p.consumerType')
                                    ->get();

            $consumerTypeTotalRecords =[];
            foreach ($consumerTypeTotals as $consumerTypeTotal) {
               $consumerType = $consumerTypeTotal->consumerType? $consumerTypeTotal->consumerType:"OTHER";
               $consumerTypeTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                                             'numberOfTransactions' => $consumerTypeTotal->numberOfTransactions,
                                             'totalAmount'=>$consumerTypeTotal->totalAmount, 'year' => $params['theYear'], 
                                             'dateOfTransaction' => $theDate->format('Y-m-d'),'consumerType' => $consumerType];
            }

            $currentEntries = DashboardConsumerTypeTotals::where([
                        ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                        ['client_id', '=', $params['client_id']],
                     ])
                     ->pluck('id')
                     ->toArray();

            DashboardConsumerTypeTotals::destroy($currentEntries);

            DashboardConsumerTypeTotals::upsert(
                     $consumerTypeTotalRecords,
                     ['client_id','consumerType', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
         //

         //Step 3 - Generate Hourly transactions totals
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

         //Step 4 Generate Payments Provider Daily transactions totals
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

         //Step 5 - Generate Payment Status Daily transactions totals         
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

         //Step 6 - Generate Payment Type Daily transactions totals
            $menuTotals = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->join('client_menus as cm','p.menu_id','=','cm.id')
                              ->select(DB::raw('cm.id, cm.prompt AS paymentType,
                                                   COUNT(p.id) AS numberOfTransactions,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->where('p.created_at', '>=' ,$params['dateFrom'])
                              ->where('p.created_at', '<=', $params['dateTo'])
                              ->whereIn('p.paymentStatus', 
                                       [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                          PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                              ->where('cw.client_id', '=', $params['client_id'])
                              ->groupBy('cm.id','cm.prompt')
                              ->get();
            $menuTotalRecords =[];
            foreach ($menuTotals as  $menuTotal) {
               $menuTotalRecords[] = ['client_id' => $params['client_id'],'menu_id' => $menuTotal->id,'paymentType' => $menuTotal->paymentType, 
                                 'year' => $params['theYear'], 'numberOfTransactions' => $menuTotal->numberOfTransactions
                                 ,'month' => $params['theMonth'],'dateOfTransaction' => $theDate->format('Y-m-d'),
                                 'day' => $params['theDay'],'totalAmount'=>$menuTotal->totalAmount];
            }

            DashboardPaymentTypeTotals::upsert(
                                          $menuTotalRecords,
                                          ['client_id','menu_id','paymentType', 'dateOfTransaction'],
                                          ['numberOfTransactions','totalAmount','year','month','day']
                                       );
         //

         //Step 7 - Generate Revenue Collector Daily transactions totals
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


            $currentEntries = DashboardRevenueCollectorTotals::where([
                        ['dateOfTransaction', '=', $theDate->format('Y-m-d')],
                        ['client_id', '=', $params['client_id']],
                     ])
                     ->pluck('id')
                     ->toArray();

            DashboardRevenueCollectorTotals::destroy($currentEntries);

            DashboardRevenueCollectorTotals::upsert(
                        $revenueCollectorTotalRecords,
                        ['client_id','revenueCollector', 'dateOfTransaction'],
                        ['numberOfTransactions','totalAmount','year','month','day']
                     );
         //

         //Step 8 - Generate RevenuePoint Daily transactions totals
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

      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

}
