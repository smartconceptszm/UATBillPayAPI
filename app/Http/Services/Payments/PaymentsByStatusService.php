<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentsByStatusService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->select('p.*')
                        ->where('p.created_at', '>=' ,$dto->dateFrom)
                        ->where('p.created_at', '<=',  $dto->dateTo)
                        ->where('p.paymentStatus', '=', $dto->paymentStatus)
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
         $records = DB::table('dashboard_payment_status_totals as p')
                        ->select(DB::raw('p.paymentStatus,
                                             SUM(p.numberOfTransactions) AS numberOfTransactions,
                                                SUM(p.totalAmount) as totalAmount'))
                        ->where('p.dateOfTransaction', '>=' ,$dto->dateFrom)
                        ->where('p.dateOfTransaction', '<=',  $dto->dateTo)
                        ->where('p.client_id', '=', $dto->client_id)
                        ->groupBy('p.paymentStatus')
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
