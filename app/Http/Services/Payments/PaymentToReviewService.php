<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentToReviewService
{

   public function findAll(array $criteria):array|null
   {

      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                        ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                        ->select('p.id','p.paymentStatus', 'p.error');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at', [$dto->dateFrom, $dto->dateTo]);
         }
         $records =$records->whereIn('p.paymentStatus', ['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED'])
                           ->where('cw.client_id', '=', $dto->client_id)
                           ->get();
         $providerErrors = $records->filter(
               function ($item) {
                  if (
                        (\strpos($item->error,"Status Code"))
                        || (\strpos($item->error,"on get transaction status"))
                        || (\strpos($item->error,"Token error"))
                        || (\strpos($item->error,"on collect funds"))
                        || ($item->paymentStatus == "SUBMITTED")
                        || ($item->paymentStatus == "SUBMISSION FAILED")
                     ) 
                  {
                     return $item;
                  }
               }
            )->values();
         return $providerErrors->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

   public function findById(string $id):object|null{

      try {
         return DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('sessions as s','p.session_id','=','s.id')
                     ->join('client_menus as cm','p.menu_id','=','cm.id')
                     ->join('clients as c','cw.client_id','=','c.id')
                     ->select('p.*','cw.client_id','cw.payments_provider_id','s.sessionId','s.mno_id','s.customerJourney',
                                 'c.shortCode','c.urlPrefix','cw.handler as walletHandler')
                     ->where('p.id', '=', $id)
                     ->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

   public function findByTransactionId(string $transactionId):object|null{

      try {
         return DB::table('payments as p')
                     ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                     ->join('sessions as s','p.session_id','=','s.id')
                     ->join('client_menus as cm','p.menu_id','=','cm.id')
                     ->join('clients as c','cw.client_id','=','c.id')
                     ->select('p.*','cw.client_id','cw.payments_provider_id','s.sessionId','s.mno_id','s.customerJourney',
                                 'c.shortCode','c.urlPrefix','cw.handler as walletHandler')
                     ->where('p.transactionId', '=', $transactionId)
                     ->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
