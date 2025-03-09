<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentsByTypeService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                        ->join('client_menus as m','p.menu_id','=','m.id')
                        ->select('p.*','m.prompt as paymentType','pp.shortName as paymentProvider')
                        ->where('p.created_at', '>=' ,$dto->dateFrom)
                        ->where('p.created_at', '<=',  $dto->dateTo)
                        ->whereIn('p.paymentStatus', 
                                 [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->where('m.id', '=', $dto->id)
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   public function summary(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->join('client_menus as cm','p.menu_id','=','cm.id')
                        ->select(DB::raw('cm.id,cm.prompt AS paymentType,
                                             COUNT(p.id) AS numberOfTransactions,
                                                SUM(p.receiptAmount) as totalAmount'))
                        ->where('p.created_at', '>=' ,$dto->dateFrom)
                        ->where('p.created_at', '<=',  $dto->dateTo)
                        ->whereIn('p.paymentStatus', 
                                 [PaymentStatusEnum::NoToken->value,PaymentStatusEnum::Paid->value,
                                    PaymentStatusEnum::Receipted->value,PaymentStatusEnum::Receipt_Delivered->value])
                        ->where('cw.client_id', '=', $dto->client_id)
                        ->groupBy('id','paymentType')
                        ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
