<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class RevenuePointViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $thePayments = DB::table('dashboard_revenue_point_totals as ddt')
                           ->select(DB::raw('ddt.revenuePoint,
                                             SUM(ddt.numberOfTransactions) AS totalTransactions,
                                             SUM(ddt.totalAmount) as totalRevenue'))
                           ->whereBetween('ddt.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('ddt.client_id', '=', $dto->client_id)
                           ->groupBy('ddt.revenuePoint');
         $thePayments= $thePayments->get();

         $theLabels = $thePayments->map(function ($item) {
                              return $item->revenuePoint.' ('.number_format($item->totalTransactions,0,'.',',').')';
                           });

         $theData = $thePayments->pluck('totalRevenue')->unique()->values();

         $colours = ChartColours::getColours(3);
         $datasets = [collect([
                        'label'=>'Collections by Revenue Point',
                        'data'=>$theData->toArray(),
                        'backgroundColor'=> $colours['backgroundColor'],
                        'borderColor' => $colours['borderColor'],
                        'pointBackgroundColor' => $colours['pointBackgroundColor'],
                        'pointBorderColor' => $colours['pointBorderColor'],
                        'fill' => false
                     ])];

         $response = [
                        'labels' =>$theLabels,
                        'datasets' =>$datasets,
                     ];
   
         return $response;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
