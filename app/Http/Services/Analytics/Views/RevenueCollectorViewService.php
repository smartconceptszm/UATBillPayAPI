<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class RevenueCollectorViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         //7. RevenueCollector Totals for Month
            $theCollectorPayments = DB::table('dashboard_revenue_collector_totals as drct')
                                       ->select(DB::raw('drct.revenueCollector,
                                                         SUM(drct.numberOfTransactions) AS totalTransactions,
                                                         SUM(drct.totalAmount) as totalRevenue'))
                                       ->whereBetween('drct.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                                       ->where('drct.client_id', '=', $dto->client_id)
                                       ->groupBy('drct.revenueCollector');
            $theCollectorPayments = $theCollectorPayments->get();

            $theLabels = $theCollectorPayments->pluck('revenueCollector')->unique()->values();
            $i=0;
            $theData = $theCollectorPayments->map(function ($item) use(&$i)  {
                           $i++;
                           $colours = ChartColours::getColours($i);
                           return [
                              'label'=>$item->revenueCollector.
                                          ' ('.number_format($item->totalTransactions,0,'.',',').')',
                              'data'=>$item->totalRevenue,
                              'backgroundColor'=> $colours['backgroundColor'],
                              'borderColor' => $colours['borderColor'],
                              'pointBackgroundColor' => $colours['pointBackgroundColor'],
                              'pointBorderColor' => $colours['pointBorderColor'],
                              'fill' => false
                           ];
                        });
         //
         $response = [
                        'labels' =>$theLabels,
                        'datasets' =>$theData 
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
