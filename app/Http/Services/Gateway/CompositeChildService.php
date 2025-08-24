<?php

namespace App\Http\Services\Gateway;

use App\Models\ClientCustomer;
use Exception;

class CompositeChildService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $records = ClientCustomer:: from('client_customers as cc1')
                        ->leftJoin('client_customers as cc2', 'cc1.parent_id', '=', 'cc2.id')
                        ->select('cc1.*')
                        ->where('cc2.customerAccount',$criteria['parentAccount'])
                        ->where('cc2.client_id',$criteria['client_id'])
                        ->get();              
         return $records->toArray();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

   

}