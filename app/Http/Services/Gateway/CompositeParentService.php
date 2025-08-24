<?php

namespace App\Http\Services\Gateway;

use App\Models\ClientCustomer;
use Exception;

class CompositeParentService
{

   public function findOneBy(array $criteria):object|null
   {

      try {
         $item = ClientCustomer::select('*')
                        ->where('customerAccount',$criteria['customerAccount'])
                        ->where('client_id',$criteria['client_id'])
                        ->get();              
         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}