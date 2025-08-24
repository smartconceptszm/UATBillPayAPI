<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Exception;

class CompositePaymentsService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('client_customers as cc','cw.client_id','=','cc.client_id')
                     ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                     ->join('client_menus as m','p.menu_id','=','m.id')
                     ->select('p.*','cc.customerName','m.prompt as paymentType',
                                                         'pp.shortName as paymentProvider');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->where('p.created_at', '>=' ,$dto->dateFrom)
                                 ->where('p.created_at', '<=', $dto->dateTo);
         }
         if(\array_key_exists('customerAccount',$criteria)){
            $records = $records->where('p.customerAccount', '=', $dto->customerAccount);
         }
         if(\array_key_exists('mobileNumber',$criteria)){
            $records = $records->where('p.mobileNumber', '=', $dto->mobileNumber);
         }
         $records = $records->whereIn('p.paymentStatus', 
                                 [  PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::NoToken->value,
                                    PaymentStatusEnum::Receipted->value,
                                    PaymentStatusEnum::Receipt_Delivered->value,
                                 ])
                           ->where('cc.customerAccount', '=', $dto->customerAccount)
                           ->where('cw.client_id', '=', $dto->client_id)
                           ->where('cc.composite', '=', 'PARENT');
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $records = $records->orderByDesc('p.created_at')
                           ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function findById(string $id):object|null
   {

      try {
         $record = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                     ->join('client_customers as cc', function ($join) {
                                 $join->on('cc.client_id', '=', 'cw.client_id')
                                    ->on('cc.customerAccount', '=', 'p.customerAccount');
                           })
                     ->join('client_menus as m','p.menu_id','=','m.id')
                     ->select('p.*','cc.customerName','m.prompt as paymentType','pp.shortName as paymentProvider')
                     ->where('p.id', '=', $id)
                     ->first();
         return $record;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
