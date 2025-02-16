<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardConsumerTypeTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ConsumerTypeAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
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

         DashboardConsumerTypeTotals::upsert(
                  $consumerTypeTotalRecords,
                  ['client_id','consumerType', 'dateOfTransaction'],
                  ['numberOfTransactions','totalAmount','year','month','day']
               );

      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         $thePayments = DB::table('dashboard_consumer_type_totals as ctt')
                           ->select(DB::raw('ctt.consumerType,
                                             SUM(ctt.numberOfTransactions) AS totalTransactions,
                                             SUM(ctt.totalAmount) as totalRevenue'))
                           ->whereBetween('ctt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ctt.client_id', '=', $dto->client_id)
                           ->groupBy('ctt.consumerType');

         $byConsumerType = $thePayments->get();

         $consumerTypeLabels = $byConsumerType->map(function ($item) {
                                             return $item->consumerType;
                                          });

         $consumerTypeData = $byConsumerType->map(function ($item) {
                                          return $item->totalRevenue;
                                       });

         $response = [
                        'consumerTypeLabels' => $consumerTypeLabels,
                        'consumerTypeData' => $consumerTypeData,
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }


}
