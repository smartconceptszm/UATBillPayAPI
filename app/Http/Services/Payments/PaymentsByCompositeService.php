<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class PaymentsByCompositeService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dateFrom = Carbon::parse($dto->dateFrom)->startOfDay()->format('Y-m-d H:i:s');
         $dateTo = Carbon::parse($dto->dateTo)->endOfDay()->format('Y-m-d H:i:s');
         $records = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('client_customers as cc','cw.client_id','=','cc.client_id')
                     ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                     ->join('client_menus as m','p.menu_id','=','m.id')
                     ->select('p.*','cc.customerName','m.prompt as paymentType','pp.shortName as paymentProvider')
                     ->where('p.created_at', '>=' ,$dateFrom)
                     ->where('p.created_at', '<=',  $dateTo)
                     ->whereIn('p.paymentStatus', 
                              [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                 PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                     ->where('cw.client_id', '=', $dto->client_id)
                     ->where('cc.composite', '=', 'PARENT');
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $records = $records->get();

         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
