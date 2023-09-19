<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentSessionService
{

   public function findAll(array $criteria=null):array|null{
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('sessions as s')
            ->join('mnos as m','s.mno_id','=','m.id')
            ->leftJoin('payments as p','p.session_id','=','s.id')
            ->leftJoin('other_payment_types as opt','p.other_payment_type_id','=','opt.id')
            ->select('s.id as session_id','s.sessionId','s.created_at','s.menu','s.mobileNumber',
                     's.accountNumber','s.customerJourney','s.status','p.id','p.transactionId',
                     'p.district','p.receiptAmount','p.receiptNumber','opt.name as paymentType',
                     'p.paymentStatus','m.name as mno','p.mnoTransactionId','p.channel','p.error')
            ->whereIn('s.menu',['PayBill','BuyUnits','OtherPayments'])
            ->where('s.client_id', '=', $dto->client_id);
         if($dto->accountNumber){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if($dto->mobileNumber && !$dto->accountNumber){
            $records = $records->where('s.mobileNumber', '=', $dto->mobileNumber);
         }
         if($dto->mobileNumber && $dto->accountNumber){
            $records = $records->orWhere('s.mobileNumber', '=', $dto->mobileNumber);
         }
         $records = $records->get();
         $records =$records->map(function($row){
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
