<?php

namespace App\Http\Services\USSD;

use Illuminate\Support\Facades\DB;
use Exception;

class SessionsOfClientService
{

   public function findAll(array $criteria = null):array|null{

      try {
         $dto=(object)$criteria;
         $records = DB::table('sessions as s')
                  ->select('*')
                  ->where('s.client_id', '=', $dto->client_id);
         if($dto->from && $dto->to){
               $records =$records->whereDate('s.created_at', '>=', $dto->from)
                                 ->whereDate('s.created_at', '<=', $dto->to);
         }
         $records =$records->get();
         return $records->all();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }
   }

}
