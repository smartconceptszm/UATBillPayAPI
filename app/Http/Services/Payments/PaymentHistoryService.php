<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentHistoryService
{

   public function findAll(array $criteria = null):array|null
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

   

}
