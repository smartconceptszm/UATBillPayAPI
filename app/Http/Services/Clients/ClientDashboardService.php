<?php

namespace App\Http\Services\Clients;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ClientDashboardService 
{

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         //Get all in Date Range
            $thePayments = DB::table('payments as p')
               ->join('mnos as m','p.mno_id','=','m.id')
               ->select('p.id','p.district','p.receiptAmount',
                                 'm.name as mno','m.colour')
               ->whereBetween('p.created_at', [$dto->dateFrom, $dto->dateTo])
               ->where('p.client_id', '=', $dto->client_id)
               ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED']);
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $thePayments = $thePayments->get();
            $groupedData = $thePayments->groupBy('district');
            $byDistrict=[];
            foreach ($groupedData as $key => $value) {
               $byDistrict[] = [
                  "district"=>$key,
                  "totalRevenue" => $value->sum('receiptAmount')
                  ];
            }
            $groupedData = $thePayments->groupBy('mno');
            $byMNO=[];
            foreach ($groupedData as $key => $value) {
               $firstRow = $value->first();
               $byMNO[]= [
                  "mno"=>$key,
                  "colour"=>$firstRow->colour,
                  "totalRevenue"=>$value->sum('receiptAmount')
                  ];
            }

         // Get all Days in Current Month
            $theDateTo = Carbon::parse($dto->dateTo);
            $currentYear = $theDateTo->format('Y');
            $currentMonth = $theDateTo->format('m');
            $firstDayOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01 00:00:00';
            $dailyTrends = DB::table('payments as p')
               ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                    COUNT(p.id) AS noOfPayments,
                                       SUM(p.receiptAmount) as totalAmount'))
               ->whereBetween('p.created_at',  [$firstDayOfCurrentMonth, $theDateTo])
               ->where('p.client_id', '=', $dto->client_id)
               ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
               ->groupBy('dayOfTx')
               ->orderBy('dayOfTx');
               // $theSQLQuery = $dailyTrends->toSql();
               // $theBindings = $dailyTrends->getBindings();
               // $rawSql2 = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
               $dailyTrends = $dailyTrends->get();
         //
         // Get collection over last one year
            $theDateFrom = Carbon::parse($dto->dateFrom);
            $startDate  = $theDateFrom->subYear(1);
            $myYear = $startDate->format('Y');
            $myMonth = $startDate->format('m');
            $myMonth = (int)$myMonth +1;
            if($myMonth>12){
               $myMonth=1;
               $myYear = (int)$myYear + 1;
            }
            $myMonth = $myMonth > 9? $myMonth:"0".$myMonth;
            $dateFrom = $myYear . '-' . $myMonth . '-01 00:00:00';
            $paymentsTrends =  DB::table('payments as p')
               ->join('mnos as m','p.mno_id','=','m.id')
               ->select(DB::raw('SUM(p.receiptAmount) as totalAmount, 
                                    month(p.created_at) as monthOfTx,
                                    m.name as mno,m.colour'))
               ->whereBetween('p.created_at',  [$dateFrom, $dto->dateTo])
               ->where('p.client_id', '=', $dto->client_id)
               ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
               ->groupBy('monthOfTx', 'mno', 'colour')
               ->orderBy('mno');
               // $theSQLQuery = $paymentsTrends->toSql();
               // $theBindings = $paymentsTrends->getBindings();
               // $rawSql3 = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
               $paymentsTrends = $paymentsTrends->get();
            $response=[
                  'payments' => $byMNO,
                  'byDistrict' => $byDistrict,
                  'paymentsTrends' => $paymentsTrends->toArray(),
                  'dailyTrends' => $dailyTrends->toArray(),
               ];
         //
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
