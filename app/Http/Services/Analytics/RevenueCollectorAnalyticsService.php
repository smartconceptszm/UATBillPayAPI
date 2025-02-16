<?php

namespace App\Http\Services\Analytics;

use App\Models\DashboardRevenueCollectorTotals;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Auth\UserService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class RevenueCollectorAnalyticsService
{

   public function __construct(
		private UserService $userService)
	{}

   public function generate(array $params)
   {
      
      try {
         $theDate = $params['theDate'];
         $revenueCollectorTotals = DB::table('payments as p')
                  ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                  ->select(DB::raw('p.revenueCollector,
                                       COUNT(p.id) AS numberOfTransactions,
                                          SUM(p.receiptAmount) as totalAmount'))
                  ->where('p.created_at', '>=' ,$params['dateFrom'])
                  ->where('p.created_at', '<=', $params['dateTo'])
                  ->whereIn('p.paymentStatus', 
                           [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                              PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                  ->where('cw.client_id', '=', $params['client_id'])
                  ->groupBy('p.revenueCollector')
                  ->get();

         $revenueCollectorTotalRecords =[];
         foreach ($revenueCollectorTotals as $revenueCollectorTotal) {
            $revenueCollector = "(POS) Point of Sale";
            if($revenueCollectorTotal->revenueCollector){
               $theUser = $this->userService->findOneBy(['client_id'=>$params['client_id'],
                                                         'revenueCollectorCode'=>$revenueCollectorTotal->revenueCollector]);
               if($theUser){
                  $revenueCollector = "(".$theUser->revenueCollectorCode.") ".$theUser->fullnames;
               }
            }
            $revenueCollectorTotalRecords[] = ['client_id' => $params['client_id'],'month' => $params['theMonth'],'day' => $params['theDay'], 
                              'numberOfTransactions' => $revenueCollectorTotal->numberOfTransactions,
                              'totalAmount'=>$revenueCollectorTotal->totalAmount, 'year' => $params['theYear'], 
                              'dateOfTransaction' => $theDate->format('Y-m-d'),'revenueCollector' => $revenueCollector];
         }

         DashboardRevenueCollectorTotals::upsert(
                     $revenueCollectorTotalRecords,
                     ['client_id','revenueCollector', 'dateOfTransaction'],
                     ['numberOfTransactions','totalAmount','year','month','day']
                  );
      } catch (\Throwable $e) {
         Log::info($e->getMessage());
         return false;
      }

      return true;
      
   }

   public function findAll(array $criteria):array|null
   {
      
      try {

         $dto = (object)$criteria;

         $dateFrom = Carbon::parse($dto->dateFrom);
         $dateFromYMD = $dateFrom->copy()->format('Y-m-d');

         $dateTo = Carbon::parse($dto->dateTo);
         $dateToYMD = $dateTo->copy()->format('Y-m-d');

         //7. RevenueCollector Totals for Month
            $theCollectorPayments = DB::table('dashboard_revenue_collector_totals as drct')
                  ->select(DB::raw('drct.revenueCollector,
                                    SUM(drct.numberOfTransactions) AS totalTransactions,
                                    SUM(drct.totalAmount) as totalRevenue'))
                  ->whereBetween('drct.dateOfTransaction', [$dateFromYMD, $dateToYMD])
                  ->where('drct.client_id', '=', $dto->client_id)
                  ->groupBy('drct.revenueCollector');
            $byRevenueCollector = $theCollectorPayments->get();
            $revenueCollectorLabels = $byRevenueCollector->map(function ($item) {
                                                return $item->revenueCollector;
                                             });
            $revenueCollectorData = $byRevenueCollector->map(function ($item) {
                                             return $item->totalRevenue;
                                          });
         //
         $response = [
                        'revenueCollectorLabels' =>$revenueCollectorLabels,
                        'revenueCollectorData' =>$revenueCollectorData 
                     ];
   
         return $response;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
