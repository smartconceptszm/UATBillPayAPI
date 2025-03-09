<?php

namespace App\Http\Services\Gateway;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentsMenuService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $records = DB::table('client_menus as cm')
                     ->join('client_menus as cm2','cm2.id','=','cm.parent_id')
                     ->select('cm.*','cm2.parent_id as rootMenu')
                     ->where('cm.client_id', '=', $criteria['client_id'])
                     ->where('cm.isPayment', '=', 'YES')
                     ->where('cm2.parent_id', '=', '0')
                     ->get()->all();               
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function submenus(array $criteria):array|null
   {

      try {
         $records = DB::table('client_menus')
                     ->select('*')
                     ->where('parent_id', '=', $criteria['parent_id'])
                     ->where('isPayment', '=', 'YES')
                     ->get()->all();               
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}