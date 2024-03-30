<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentSessionService
{

   public function findAll(array $criteria):array|null{
      try {
         $dto = (object)$criteria;
         $records = DB::table('sessions as s')
            ->join('mnos','s.mno_id','=','mnos.id')
            ->join('client_menus as m','s.menu_id','=','m.id')
            ->leftJoin('payments as p','p.session_id','=','s.id')
            ->select('s.id as session_id','s.sessionId','s.created_at','s.mobileNumber','s.accountNumber',
                     's.customerJourney','s.status','m.prompt as paymentType','p.id','p.transactionId',
                     'p.district','p.receiptAmount','p.receiptNumber','p.receipt','p.tokenNumber',
                     'p.paymentStatus','mnos.name as mno','p.mnoTransactionId','p.meterNumber',
                     'p.channel','p.error');
         if($dto->accountNumber){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if(\property_exists($dto,'mobileNumber') && $dto->accountNumber){
            $records = $records->where('s.mobileNumber', '=', $dto->mobileNumber);
         }
         $records = $records->where('s.client_id', '=', $dto->client_id)
                              ->where('m.isPayment','=', 'YES')
                              ->orderByDesc('s.created_at');
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
