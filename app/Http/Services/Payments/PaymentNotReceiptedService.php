<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentNotReceiptedService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto=(object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                  ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                  ->join('payments_providers as pps','cw.payments_provider_id','=','pps.id')
                  ->join('clients as c','cw.client_id','=','c.id')
                  ->join('sessions as s','p.session_id','=','s.id')
                  ->join('client_menus as m','p.menu_id','=','m.id')
                  ->select('p.*','pps.shortName as paymentProvider','m.prompt as paymentType','cw.paymentMethod');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at',[$dto->dateFrom, $dto->dateTo]);
         }
         $records = $records->where('c.id', '=', $dto->client_id)
                              ->whereIn('p.paymentStatus', [PaymentStatusEnum::NoToken->value,
                                                               PaymentStatusEnum::Paid->value,
                                                               PaymentStatusEnum::Receipted->value])
                              ->orderByDesc('p.created_at')->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
