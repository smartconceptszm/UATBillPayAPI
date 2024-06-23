<?php

namespace App\Http\Services\Web\Clients;

use Illuminate\Support\Facades\DB;
use Exception;

class MnosOfClientService
{

   public function findAll(string $client_id):array|null{

      try {
         $records = DB::table('client_mnos as cm')
                  ->join('mnos as m','cm.mno_id','=','m.id')
                  ->select('cm.*','m.name')
                  ->where('cm.client_id', '=', $client_id);
         $records =$records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }

}
