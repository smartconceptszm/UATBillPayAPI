<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentTransactionService
{

   public function findAll(array $criteria = null):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('payments as p')
            ->join('sessions as s','p.session_id','=','s.id')
            ->join('mnos as m','p.mno_id','=','m.id')
            ->leftJoin('other_payment_types as opt','p.other_payment_type_id','=','opt.id')
            ->select('p.id','p.created_at','p.transactionId','p.accountNumber','p.district','p.mobileNumber',
                     'p.receiptAmount','p.receiptNumber','s.menu','opt.name as paymentType','p.paymentStatus',
                     'm.name as mno','p.mnoTransactionId','p.channel','p.error')
            ->whereIn('p.paymentStatus',['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
            ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereDate('p.created_at', '>=', $dto->dateFrom)
                              ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records = $records->get();
         $records = $records->map(function($row){
            if(!$row->paymentType){
               $row->paymentType = $row->menu;
            }
            return $row;
         });
         return $records->all();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
