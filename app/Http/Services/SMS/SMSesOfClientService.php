<?php

namespace App\Http\Services\SMS;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class SMSesOfClientService
{

   public function findAll(array $criteria = null):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('messages')
            ->select('*')
            ->where('client_id', '=', $dto->client_id)
            ->orderByDesc('created_at');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereBetween(DB::raw('DATE(created_at)'), [$dto->dateFrom, $dto->dateTo]);
         }
         $records = $records->get();
         return $records->all();
      } catch (Exception$e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
