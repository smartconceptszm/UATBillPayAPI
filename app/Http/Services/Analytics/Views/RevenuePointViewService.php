<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class RevenuePointViewService
{

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
