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
            ->join('mnos','s.mno_id','=','mnos.id')
            ->join('client_menus as m','s.menu_id','=','m.id')
            ->leftJoin('payments as p','p.session_id','=','s.id')
            ->select('s.id as session_id','s.sessionId','s.created_at','s.mobileNumber','s.accountNumber',
                     's.customerJourney','s.status','m.prompt as paymentType','p.id','p.transactionId',
                     'p.district','p.receiptAmount','p.receiptNumber','p.paymentStatus',
                     'mnos.name as mno','p.mnoTransactionId',
                     'p.channel','p.error')
            ->where('m.isPayment','=', 'YES')
            ->where('s.client_id', '=', $dto->client_id);
         if($dto->accountNumber){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if(\property_exists($dto,'mobileNumber') && $dto->accountNumber){
            $records = $records->orWhere('s.mobileNumber', '=', $dto->mobileNumber);
         }
         $records = $records->get();
         return $records->all();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
