<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentsByRevenueCollectorService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom)->startOfDay()->format('Y-m-d H:i:s');
         $dateTo = Carbon::parse($dto->dateTo)->endOfDay()->format('Y-m-d H:i:s');
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->select('p.*')
                        ->where('p.created_at', '>=' ,$dateFrom)
                        ->where('p.created_at', '<=',  $dateTo)
                        ->whereIn('p.paymentStatus', 
                                 [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('p.revenueCollector', '=', $dto->revenueCollector)
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function summary(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('dashboard_revenue_collector_totals as p')
                        ->select(DB::raw('p.revenueCollector,
                                             SUM(p.numberOfTransactions) AS numberOfTransactions,
                                                SUM(p.totalAmount) as totalAmount'))
                        ->where('p.dateOfTransaction', '>=' ,$dto->dateFrom)
                        ->where('p.dateOfTransaction', '<=',  $dto->dateTo)
                        ->where('p.client_id', '=', $dto->client_id)
                        ->groupBy('p.revenueCollector')
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
