<?php

namespace App\Http\Services\Analytics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class MainDashboardService 
{

   public function findAll(array $criteria)
   {
      
      try {
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m',$dto->theMonth);
         $theYear = (string)$theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         //Get all in Date Range
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                           ->join('clients as c','ppt.client_id','=','c.id')
                           ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                           ->select('ppt.*','c.name','c.urlPrefix','p.shortName as paymentsProvider','p.colour')
                           ->where('ppt.year', '=',  $theYear)
                           ->where('ppt.month', '=',  $theMonth)
                           ->orderBy('ppt.totalAmount','desc')
                           ->get();
            $groupedData = $thePayments->groupBy('urlPrefix');
         //
         return $groupedData;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
