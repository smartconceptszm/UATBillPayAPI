<?php

namespace App\Http\Services\Analytics;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ClientDashboardService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $theDate = Carbon::createFromFormat('Y-m',$dto->theMonth);
         $theYear = (string)$theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         //1. Payments Provider Totals for Month
            $thePayments = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select('ppt.*','p.shortName as paymentsProvider','p.colour')
                              ->where('ppt.year', '=',  $theYear)
                              ->where('ppt.month', '=',  $theMonth)
                              ->where('ppt.client_id', '=', $dto->client_id);
            // $theSQLQuery = $thePayments->toSql();
            // $theBindings = $thePayments-> getBindings();
            // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $byPaymentProvider = $thePayments->get();
         //

         //2. District Totals for Month
            $thePayments = DB::table('dashboard_district_totals as ddt')
                                 ->select('ddt.*')
                                 ->where('ddt.year', '=',  $theYear)
                                 ->where('ddt.month', '=',  $theMonth)
                                 ->where('ddt.client_id', '=', $dto->client_id);
            $byDistrict = $thePayments->get();
         //
         //3. Payments Status Totals for Month
            $thePayments = DB::table('dashboard_payment_status_totals as pst')
                              ->select('pst.*')
                              ->where('pst.year', '=',  $theYear)
                              ->where('pst.month', '=',  $theMonth)
                              ->where('pst.client_id', '=', $dto->client_id);
            $thePayments = $thePayments->get();
         
            $paymentStatusColours = [
                                          'PAID | NOT RECEIPTED'=>'#E3370F',
                                          'RECEIPT DELIVERED'=>'#2DB75C',
                                          'RECEIPTED'=>'#1486E4',
                                       ];
            $byPaymentStatus = [];
            foreach ($thePayments as $key => $value) {
               $byPaymentStatus[$key] = array_merge(\get_object_vars($value),['colour'=>$paymentStatusColours[$value->paymentStatus]]);
            }
         //
         //4. Daily Totals over the Month
            $thePayments = DB::table('dashboard_daily_totals as dt')
                        ->select('dt.*')
                        ->where('dt.year', '=',  $theYear)
                        ->where('dt.month', '=',  $theMonth)
                        ->where('dt.client_id', '=', $dto->client_id)
                        ->orderBy('day');
            $dailyTrends = $thePayments->get();
         //
         // 5 Monthly Totals Over one Year
            $myYear = $theDate->format('Y');
            $myMonth = $theDate->format('m');
            $paymentsTrends = DB::table('dashboard_payments_provider_totals as ppt')
                              ->join('payments_providers as p','ppt.payments_provider_id','=','p.id')
                              ->select('ppt.*','p.shortName as paymentsProvider','p.colour')
                              ->where('ppt.client_id', '=', $dto->client_id)
                              ->where('ppt.year', '=',  $myYear)
                              ->where('ppt.month', '=',  $myMonth);
            $startDate  = $theDate->subYear(1);
            for ($i=1; $i < 12; $i++) { 
               $startDate = $startDate->addMonth();
               $myYear = $startDate->format('Y');
               $myMonth = $startDate->format('m');
               $client_id= $dto->client_id;
               $paymentsTrends = $paymentsTrends->orWhere(function (Builder $query) use($myYear,$myMonth,$client_id) {
                                                         $query->where('ppt.year','=', $myYear)
                                                               ->where('ppt.month','=', $myMonth)
                                                               ->where('ppt.client_id','=', $client_id);
                                                   });
            }
            // $theSQLQuery = $paymentsTrends->toSql();
            // $theBindings = $paymentsTrends->getBindings();
            // $rawSql3 = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
            $paymentsTrends = $paymentsTrends->get();
         //

         $response = [
                        'payments' => $byPaymentProvider->toArray(),
                        'byPaymentStatus' => $byPaymentStatus,
                        'byDistrict' => $byDistrict->toArray(),
                        'paymentsTrends' => $paymentsTrends->toArray(),
                        'dailyTrends' => $dailyTrends->toArray(),
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
