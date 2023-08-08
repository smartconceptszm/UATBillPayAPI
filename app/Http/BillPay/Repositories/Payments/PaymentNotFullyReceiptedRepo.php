<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentNotFullyReceiptedRepo implements IFindAllRepository
{

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $dto=(object)$criteria;
         $records = DB::table('payments as p')
                  ->join('mnos as m','p.mno_id','=','m.id')
                  ->select('p.id','p.created_at','p.mobileNumber','p.accountNumber','p.receiptNumber',
                           'p.receiptAmount','p.paymentAmount','p.transactionId','p.district',
                           'p.mnoTransactionId','m.name as mno','p.paymentStatus','p.error')
                  ->whereIn('p.paymentStatus', ['PAID | NOT RECEIPTED','RECEIPTED'])
                  ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
               $records =$records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }
    
}
