<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Models\DashboardRevenuePointTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class RevenuePointsAnalyticsService
{

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
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

         $thePayments = DB::table('dashboard_revenue_point_totals as ddt')
                           ->select(DB::raw('ddt.revenuePoint,
                                             SUM(ddt.numberOfTransactions) AS totalTransactions,
                                             SUM(ddt.totalAmount) as totalRevenue'))
                           ->whereBetween('ddt.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                           ->where('ddt.client_id', '=', $dto->client_id)
                           ->groupBy('ddt.revenuePoint');
         $byRevenuePoint= $thePayments->get();
         $revenuePointLabels = $byRevenuePoint->map(function ($item) {
                                             return $item->revenuePoint;
                                          });
         $revenuePointData = $byRevenuePoint->map(function ($item) {
                                          return $item->totalRevenue;
                                       });

         $response = [
                        'revenuePointLabels' => $revenuePointLabels,
                        'revenuePointData' => $revenuePointData
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
