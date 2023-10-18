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
            ->join('mnos','p.mno_id','=','mnos.id')
            ->join('client_menus as m','p.menu_id','=','m.id')
            ->select('p.id','p.created_at','p.transactionId','p.accountNumber','p.district','p.mobileNumber',
                     'p.receiptAmount','p.receiptNumber','m.prompt as paymentType','p.paymentStatus',
                     'mnos.name as mno','p.mnoTransactionId','p.channel','p.error')
            ->whereIn('p.paymentStatus',['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED','PAID | NOT RECEIPTED','RECEIPTED','RECEIPT DELIVERED'])
            ->where('p.client_id', '=', $dto->client_id)
            ->orderByDesc('p.created_at');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereDate('p.created_at', '>=', $dto->dateFrom)
                              ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records = $records->get();
         return $records->all();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
