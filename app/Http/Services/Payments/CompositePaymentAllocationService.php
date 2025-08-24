<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class CompositePaymentAllocationService
{

   public function findAll(string $id):object|null{

      try {
         return DB::table('payments as p')
                     ->join('composite_receipts as cr','p.id','=','cr.payment_id')
                     ->join('client_customers as cc', function ($join) {
                              $join->on('cc.client_id', '=', 'cr.client_id')
                                 ->on('cc.customerAccount', '=', 'cr.customerAccount');
                        })
                     ->select('cr.*','cc.customerAddress','cc.customerName')
                     ->where('p.id', '=', $id)
                     ->get();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
