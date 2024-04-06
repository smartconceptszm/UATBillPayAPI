<?php

namespace App\Http\Services\USSD;

use Illuminate\Support\Facades\DB;
use Exception;

class SessionsOfClientService
{

   public function findAll(array $criteria):array|null{

      try {
         
            $dto=(object)$criteria;
         $records = DB::table('sessions as s')
                  ->join('client_menus as m','s.menu_id','=','m.id')
                  ->select('s.*','m.prompt AS menu','m.accountType')
                  ->where('s.client_id', '=', $dto->client_id);
         if(\array_key_exists('accountNumber',$criteria)){
            $records = $records->where('s.accountNumber', '=', $dto->accountNumber);
         }
         if(\array_key_exists('meterNumber',$criteria)){
            $records = $records->where('s.meterNumber', '=', $dto->meterNumber);
         }
         if(\array_key_exists('mobileNumber',$criteria)){
            $records = $records->where('s.mobileNumber', '=', $dto->mobileNumber);
         }
         if(\array_key_exists('dateFrom',$criteria) && \array_key_exists('dateTo',$criteria)){
            $records =$records->whereBetween('s.created_at', [$dto->dateFrom." 00:00:00", $dto->dateTo." 23:59:59"]);
         }
         $records =$records->orderByDesc('s.created_at')->get();
         return $records->all();

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
