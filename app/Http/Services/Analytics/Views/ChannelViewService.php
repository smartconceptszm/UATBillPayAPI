<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Exception;

class ChannelViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $thePayments = DB::table('dashboard_channel_totals as ct')
                           ->select(DB::raw('ct.channel,
                                             SUM(ct.numberOfTransactions) AS totalTransactions,
                                             SUM(ct.totalAmount) as totalRevenue'))
                           ->whereBetween('ct.dateOfTransaction', [$dto->dateFromYMD, $dto->dateToYMD])
                           ->where('ct.client_id', '=', $dto->client_id)
                           ->groupBy('ct.channel');

         $thePayments = $thePayments->get();

         $theLabels = $thePayments->map(function ($item) {
                     return $item->channel.' ('.number_format($item->totalTransactions,0,'.',',').')';
                  });

         $theData = $thePayments->pluck('totalRevenue')->unique()->values();

         $colours = ChartColours::getColours(4);
         $datasets = [collect([
                           'label'=>'Collections by Channel',
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
