<?php

namespace App\Http\Services\Web\Clients;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ClientDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         //Get all in Date Range
            $thePayments = DB::table('payments as p')
                           ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                           ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')
                           ->select('p.id','p.paymentStatus','p.district','p.receiptAmount',
                                             'pps.shortName as paymentProvider','pps.colour')
                           ->whereBetween('p.created_at', [$dto->dateFrom, $dto->dateTo])
                           ->where('cw.client_id', '=', $dto->client_id)
                           ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED']);
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $thePayments = $thePayments->get();

            $groupedData = $thePayments->groupBy('district');
            $byDistrict = [];
            foreach ($groupedData as $key => $value) {
               $byDistrict[] = [
                                 "district"=>$key,
                                 "totalRevenue" => $value->sum('receiptAmount')
                              ];
            }
            $groupedData = $thePayments->groupBy('paymentProvider');
            $byPaymentProvider = [];
            foreach ($groupedData as $key => $value) {
               $firstRow = $value->first();
               $byPaymentProvider[] = [
                              "paymentProvider" => $key,
                              "colour" => $firstRow->colour,
                              "totalRevenue" => $value->sum('receiptAmount')
                           ];
            }

            $groupedData = $thePayments->groupBy('paymentStatus');
            $byPaymentStatus = [];
            $paymentStatusColours = [
                                          'PAID | NOT RECEIPTED'=>'#E3370F',
                                          'RECEIPT DELIVERED'=>'#2DB75C',
                                          'RECEIPTED'=>'#1486E4',
                                       ];
            foreach ($groupedData as $key => $value) {
               $firstRow = $value->first();
               $byPaymentStatus[] = [
                                       "paymentStatus" =>$key,
                                       "colour"=>$paymentStatusColours[$key],
                                       "totalRevenue"=>$value->sum('receiptAmount'),
                                       "noOfPayments" =>$value->count('id')
                                    ];
            }

         // Get all Days in Current Month
               $theDateTo = Carbon::parse($dto->dateTo);
            $currentYear = $theDateTo->format('Y');
            $currentMonth = $theDateTo->format('m');
            $firstDayOfCurrentMonth = $currentYear . '-' . $currentMonth . '-01 00:00:00';
            $dailyTrends = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select(DB::raw('dayofmonth(p.created_at) as dayOfTx,
                                                   COUNT(p.id) AS noOfPayments,
                                                      SUM(p.receiptAmount) as totalAmount'))
                              ->whereBetween('p.created_at',  [$firstDayOfCurrentMonth, $theDateTo])
                              ->where('cw.client_id', '=', $dto->client_id)
                              ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED',
                                                               'RECEIPTED','RECEIPT DELIVERED'])
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
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')
                                 ->select(DB::raw('SUM(p.receiptAmount) as totalAmount, 
                                                      month(p.created_at) as monthOfTx,
                                                      pps.shortName as paymentProvider,pps.colour'))
                                 ->whereBetween('p.created_at',  [$dateFrom, $dto->dateTo])
                                 ->where('cw.client_id', '=', $dto->client_id)
                                 ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED',
                                                               'RECEIPTED','RECEIPT DELIVERED'])
                                 ->groupBy('monthOfTx', 'paymentProvider', 'colour')
                                 ->orderBy('paymentProvider');
               // $theSQLQuery = $paymentsTrends->toSql();
               // $theBindings = $paymentsTrends->getBindings();
               // $rawSql3 = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
               $paymentsTrends = $paymentsTrends->get();

               $groupedData = $paymentsTrends->groupBy('monthOfTx');
               $byMonthOfyear=[];
               foreach ($groupedData as $key => $value) {
                  $firstRow = $value->first();
                  $byMonthOfyear[]= [
                                          "monthOfTx"=>$key,
                                          "totalAmount"=>$value->sum('totalAmount')
                                       ];
               }

               $response = [
                              'payments' => $byPaymentProvider,
                              'byPaymentStatus' => $byPaymentStatus,
                              'byDistrict' => $byDistrict,
                              'paymentsTrends' => $paymentsTrends->toArray(),
                              'byMonthOfyear' => $byMonthOfyear,
                              'dailyTrends' => $dailyTrends->toArray(),
                           ];
         //
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
