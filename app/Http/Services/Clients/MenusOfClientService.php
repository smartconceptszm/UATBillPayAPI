<?php

namespace App\Http\Services\Clients;

use Illuminate\Support\Facades\DB;
use App\Models\ClientMenu;
use Exception;

class MenusOfClientService
{

   public function __construct(
         private ClientMenu $model
   ) {}

   public function levelOneMenus(string $client_id):array|null
   {

      try {
         $records = DB::table('client_menus as cm')
                     ->join('client_menus as cm2','cm2.id','=','cm.parent_id')
                     ->select('cm.*','cm2.parent_id as rootMenu')
                     ->where('cm.client_id', '=', $client_id)
                     ->where('cm2.parent_id', '=', '0')
                     ->orderBy('cm.order')
                     ->get()->all();               
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function subMenus(string $parent_id):array|null
   {

      try {
         $records = DB::table('client_menus')
                     ->select('*')
                     ->where('parent_id', '=', $parent_id)
                     ->orderBy('order')
                     ->get()->all();               
         return $records;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }




}
