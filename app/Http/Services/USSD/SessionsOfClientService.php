<?php

namespace App\Http\Services\USSD;

use Illuminate\Support\Facades\DB;
use Exception;

class SessionsOfClientService
{

   public function findAll(array $criteria = null):array|null{

      try {
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('sessions as s')
                  ->select('*')
                  ->where('s.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
               $records =$records->whereBetween('s.created_at', [$dto->dateFrom, $dto->dateTo]);
         }
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}
