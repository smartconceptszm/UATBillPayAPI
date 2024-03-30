<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentHistoryService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $records = DB::table('payments')
                        ->select('id','created_at','accountNumber','mobileNumber','receiptAmount','receiptNumber')
                        ->where('accountNumber', '=', $dto->accountNumber)
                        ->where('mobileNumber', '=', $dto->mobileNumber)
                        ->where('client_id', '=', $dto->client_id)
                        ->whereIn('paymentStatus',['PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
                        ->orderByDesc('created_at')
                        ->limit($dto->limit)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function getLatestToken(array $criteria):object|null
   {

      try {
         $dto = (object)$criteria;
         $record = DB::table('payments as p')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select('p.id','p.tokenNumber', 'p.receipt')
                        ->where('p.meterNumber', '=', $dto->meterNumber)
                        ->where('p.client_id', '=', $dto->client_id)
                        ->where('cm.isPayment', '=', "YES")
                        ->where('cm.isDefault', '=', "YES")
                        ->where('cm.accountType', '=', "PRE-PAID")
                        ->whereIn('p.paymentStatus',['RECEIPTED','RECEIPT DELIVERED'])
                        ->orderByDesc('p.created_at')
                        ->first();
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
