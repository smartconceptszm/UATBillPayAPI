<?php

namespace App\Http\Services\Analytics\Views;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class HourlySalesViewService
{

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;
         $theDate = Carbon::parse($dto->theDate);
         $dateFrom = $theDate->format('Y-m-d');

         $hourlyTrends = DB::table('dashboard_hourly_totals as dht')
                  ->select('dht.hour','dht.numberOfTransactions',
                              'dht.totalAmount')
                  ->where('dht.dateOfTransaction', '=', $dateFrom)
                  ->where('dht.client_id', '=', $dto->client_id)
                  ->orderBy('dht.hour');
         $hourlyTrends = $hourlyTrends->get();
         $hourlyLabels = $hourlyTrends->map(function ($item) {
                                    return $item->hour;
                                 });
         $hourlyData = $hourlyTrends->map(function ($item) {
                                    return $item->totalAmount;
                                 });

         $response = [
                        'hourlyLabels' => $hourlyLabels,
                        'hourlyData' =>$hourlyData,
                     ];
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
