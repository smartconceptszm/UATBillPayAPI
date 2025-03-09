<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class ConsumerTierViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $thePayments = DB::table('dashboard_consumer_tier_totals as ctt')
                           ->select(DB::raw('ctt.consumerTier,
                                             SUM(ctt.numberOfTransactions) AS totalTransactions,
                                             SUM(ctt.totalAmount) as totalRevenue'))
                           ->whereBetween('ctt.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('ctt.client_id', '=', $dto->client_id)
                           ->groupBy('ctt.consumerTier');

         $thePayments = $thePayments->get();

         $theLabels = $thePayments->map(function ($item) {
                     return $item->consumerTier.' ('.number_format($item->totalTransactions,0,'.',',').')';
                  });

         $theData = $thePayments->pluck('totalRevenue')->unique()->values();

         $colours = ChartColours::getColours(1);
         $datasets = [collect([
                           'label'=>'Collections by Consumer Tier',
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
