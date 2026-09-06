<?php

namespace App\Http\Services\PublicAPI;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\APIUser;
use Exception;

class PaymentsViaAPIMenuService
{

   public function findAll(?APIUser $apiUser):array|null
   {

      try {


         if($apiUser){
            $service_provider_id = $apiUser->service_provider_id;
         }else{
            $user = Auth::user();
            $service_provider_id = $user->service_provider_id;
         }

          
         $records = DB::table('client_menus as cm')
                     ->join('client_menus as cm2','cm2.id','=','cm.parent_id')
                     ->select('cm.id','cm.prompt','cm.description','cm.customerAccountPrompt',
                                 'cm.onOneAccount','cm.commonAccount as customerAccount',
                                 'cm.isDefault as default','cm.requiresReference','cm.referencePrompt')
                     ->where('cm.client_id', '=', $service_provider_id)
                     ->where('cm.isPayment', '=', 'YES')
                     ->whereNot('cm.handler', '=', 'ParentMenu')
                     ->orderBy('cm2.parent_id')
                     ->orderBy('cm.order')
                     ->get()->all();               
         return $records;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   public function getDefaultMenu(APIUser $apiUser):object|null
   {

      try {

         $menus = $this->findAll($apiUser);     
         return $menus[0];  

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