<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentTransactionsService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('payments_providers as pp','cw.payments_provider_id','=','pp.id')
                     ->join('client_menus as m','p.menu_id','=','m.id')
                     ->select('p.*','m.prompt as paymentType','pp.shortName as paymentProvider');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->where('p.created_at', '>=' ,$dto->dateFrom)
                                 ->where('p.created_at', '<=', $dto->dateTo);
         }
         $records = $records->where('cw.client_id', '=', $dto->client_id);
         // $theSQLQuery = $records->toSql();
         // $theBindings = $records-> getBindings();
         // $rawSql = vsprintf(str_replace(['?'], ['\'%s\''], $theSQLQuery), $theBindings);
         $records = $records->orderByDesc('p.created_at')
                           ->limit((int)$billpaySettings['PAYMENTS_QUERY_LIMIT'])
                           ->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

   

}
