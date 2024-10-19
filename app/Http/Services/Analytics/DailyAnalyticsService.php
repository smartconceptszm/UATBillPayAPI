<?php

namespace App\Http\Services\Analytics;

use App\Http\Services\Clients\ClientService;
use App\Models\DashboardDailyTotals;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class DailyAnalyticsService
{

   public function __construct(
      private DashboardDailyTotals $dashboardDailyTotals,
      private ClientService $clientService)
   {}

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         return $this->dashboardDailyTotals->where($criteria)->orderBy('day')->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function generate(Carbon $theDate)
   {
      
      try {
         $theYear = $theDate->year;
         $theMonth = \strlen((string)$theDate->month)==2?$theDate->month:"0".(string)$theDate->month;
         $theDay = \strlen((string)$theDate->day)==2?$theDate->day:"0".(string)$theDate->day;
         $dateFrom = $theYear . '-' . $theMonth . '-' .$theDay. ' 00:00:00';
         $dateTo = $theYear . '-' . $theMonth . '-' .$theDay. ' 23:59:59';
         $clients = $this->clientService->findAll(['status'=>'ACTIVE']);
         foreach ($clients as $client) {
            $dailyTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('dayofmonth(p.created_at) as day,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                                 ->where('cw.client_id', '=', $client->id)
                                 ->whereIn('p.paymentStatus', 
                                          ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                 ->groupBy('day')
                                 ->get();
            
            if($dailyTotals->isNotEmpty()){
               $dailyTotals =$dailyTotals[0];
               DashboardDailyTotals::upsert(
                        [
                           ['client_id' => $client->id, 'year' => $theYear, 'month' => $theMonth, 'day' => $dailyTotals->day,
                              'numberOfTransactions' => $dailyTotals->numberOfTransactions,'totalAmount'=>$dailyTotals->totalAmount]
                        ],
                        ['client_id','year','month','day'],
                        ['numberOfTransactions','totalAmount']
                     );
            }
         }
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }
      Log::info('(SCL) Daily transaction analytics service executed for: '.$theDate->format('d F Y'));
      return true;

   }

}
