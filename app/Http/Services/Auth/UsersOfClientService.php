<?php

namespace App\Http\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class UsersOfClientService
{

   public function findAll(array $criteria = null):array|null{

      try {
         $records = DB::table('users')
                     ->where('client_id', '=', $criteria['client_id'])
                     ->select('*')
                     ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
