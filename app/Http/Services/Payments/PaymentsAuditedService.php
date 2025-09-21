<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentsAuditedService
{

   public function findAll(array $criteria):array|null{

      try {
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')

                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')
                        ->join('clients as c','cw.client_id','=','c.id')

                        ->join('client_menus as m','p.menu_id','=','m.id')

                        ->whereExists(function ($query) {
                                    $query->select(DB::raw(1))
                                          ->from('payment_audits as pa')
                                          ->whereColumn('pa.payment_id', 'p.id');
                              })
                        ->select('p.*','pps.shortName as paymentProvider','m.prompt as paymentType',
                                    'cw.paymentMethod');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at',[$dto->dateFrom,$dto->dateTo]);
         }
         $records = $records->where('c.id', '=', $dto->client_id)
                              ->orderByDesc('p.created_at');
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function updateHistory(string $id) : object|null {
      
      try {

         $records = DB::table('payments as p')
                        ->join('payment_audits as pa','pa.payment_id','=','p.id')
                        ->join('users as u','pa.user_id','=','u.id')
                        ->select('pa.*','p.id','u.username','u.fullnames','u.mobileNumber')
                        ->where('p.id', '=', $id)
                        ->get();

         $recordsWithCommonKeys = $records->map(function ($item) {
            // Decode JSON strings to arrays
            $oldValues = json_decode($item->oldValues, true);
            $newValues = json_decode($item->newValues, true);
            
            // Find common keys
            $commonKeys = array_intersect(array_keys($oldValues), array_keys($newValues));
            
            // Create objects with only common keys
            $commonOldValues = array_intersect_key($oldValues, array_flip($commonKeys));
            $commonNewValues = array_intersect_key($newValues, array_flip($commonKeys));
            
            // Add new properties to the item
            $item->commonOldValues = json_encode($commonOldValues);
            $item->commonNewValues = json_encode($commonNewValues);
            
            return $item;
         });

            return $recordsWithCommonKeys;

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}


