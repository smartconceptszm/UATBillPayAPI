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
         $dateFrom = $theDate->copy()->startOfDay();
         $dateTo = $theDate->copy()->endOfDay();
         $theMonth = $theDate->month;
         $theYear = $theDate->year;
         $theDay = $theDate->day;

         $clients = $this->clientService->findAll(['status'=>'ACTIVE']);
         foreach ($clients as $client) {

            $dailyTotals = DB::table('payments as p')
                                 ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                                 ->select(DB::raw('cw.payments_provider_id,
                                                      COUNT(p.id) AS numberOfTransactions,
                                                         SUM(p.receiptAmount) as totalAmount'))
                                 ->whereBetween('p.created_at', [$dateFrom, $dateTo])
                                 ->where('cw.client_id', '=', $client->id)
                                 ->whereIn('p.paymentStatus', 
                                          ['PAID | NO TOKEN','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                                 ->groupBy('payments_provider_id')
                                 ->get();
            
            if($dailyTotals->isNotEmpty()){
               $dailyTotalRecords =[];
               foreach ($dailyTotals as $dailyTotal) {
                  $dailyTotalRecords[] = ['client_id' => $client->id,'payments_provider_id' => $dailyTotal->payments_provider_id, 
                                             'numberOfTransactions' => $dailyTotal->numberOfTransactions,
                                             'year' => $theYear,'month' => $theMonth, 'day' => $theDay,
                                             'dateOfTransaction' => $theDate->format('Y-m-d'),
                                             'totalAmount'=>$dailyTotal->totalAmount,];
               }
   
               DashboardDailyTotals::upsert(
                        $dailyTotalRecords,
                        ['client_id','payments_provider_id','dateOfTransaction'],
                        ['numberOfTransactions','totalAmount','year','month','day']
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
