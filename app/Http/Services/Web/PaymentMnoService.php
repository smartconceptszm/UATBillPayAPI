<?php

namespace App\Http\Services\Web;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentMnoService
{

   public function findAll(String $client_id):array|null
   {

      try {
         $records = DB::table('client_mnos as cm')
                  ->join('mnos as m','cm.mno_id','=','m.id')
                  ->select('cm.*','m.name','m.colour',)
                  ->where('cm.client_id', '=', $client_id)
                  ->where('cm.momoActive', '=', 'YES')
                  ->where('cm.momoMode', '=', 'UP')
                  ->get()->all();
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
