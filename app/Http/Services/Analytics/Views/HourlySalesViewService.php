<?php

namespace App\Http\Services\Analytics\Views;

use \App\Http\Services\Enums\ChartColours;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class HourlySalesViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $hourlyTrends = DB::table('dashboard_hourly_totals as dht')
                              ->select('dht.hour','dht.numberOfTransactions',
                                          'dht.totalAmount')
                              ->where('dht.dateOfTransaction', '=', $dto->dateToYMD)
                              ->where('dht.client_id', '=', $dto->client_id)
                              ->orderBy('dht.hour')
                              ->get();

         $hourLabels =[];
         $hourData = [];
         for ($i=0; $i < 24; $i++) { 
            $hourRecord = $hourlyTrends->firstWhere('hour','=',$i);
            $hourLabels[] = $i;
            if($hourRecord){
               $hourData[] = $hourRecord->totalAmount;
            }else{
               $hourData[] = 0;
            }
         }

         $response['labels'] = collect($hourLabels);
         $colours = ChartColours::getColours(1);
         $response['datasets'][] = collect([
                                          'backgroundColor'=> $colours['backgroundColor'],
                                          'borderColor' => $colours['borderColor'],
                                          'pointBackgroundColor' => $colours['pointBackgroundColor'],
                                          'pointBorderColor' => $colours['pointBorderColor'],
                                          'label' => 'Hourly Collections',
                                          'data' => collect($hourData),
                                          'fill' => true
                                       ]);

         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
