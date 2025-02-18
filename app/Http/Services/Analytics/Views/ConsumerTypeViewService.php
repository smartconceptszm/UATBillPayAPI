<?php

namespace App\Http\Services\Analytics\Views;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ConsumerTypeViewService
{

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
