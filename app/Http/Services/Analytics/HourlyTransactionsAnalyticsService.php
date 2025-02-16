<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardHourlyTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HourlyTransactionsAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
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
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

}
