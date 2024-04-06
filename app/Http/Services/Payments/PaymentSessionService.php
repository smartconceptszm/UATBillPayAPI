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
            ->select('p.*','s.id as session_id','s.sessionId','s.customerJourney','s.status','m.accountType',
                     'm.prompt as paymentType','mnos.name as mno');
         if(\array_key_exists('accountNumber',$criteria)){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if(\array_key_exists('meterNumber',$criteria)){
            $records = $records->where('s.meterNumber', '=', $dto->meterNumber);
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
