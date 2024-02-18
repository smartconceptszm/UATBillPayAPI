<?php

namespace App\Http\Services\Payments;

use Illuminate\Support\Facades\DB;
use Exception;

class PaymentToReviewService
{

   public function findAll(array $criteria = null):array|null
   {

      try {
         $dto = (object)$criteria;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
            ->select('id','paymentStatus', 'error');
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereBetween('p.created_at', [$dto->dateFrom, $dto->dateTo]);
         }
         $records =$records->whereIn('p.paymentStatus', ['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED'])
                           ->where('p.client_id', '=', $dto->client_id)
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
                     ->join('sessions as s','p.session_id','=','s.id')
                     ->join('mnos as m','s.mno_id','=','m.id') 
                     ->join('client_menus as cm','p.menu_id','=','cm.id')
                     ->join('clients as c','s.client_id','=','c.id')
                     ->select('p.*','s.sessionId','s.customerJourney','c.shortCode',
                              'cm.billingClient','c.urlPrefix','m.name as mnoName')
                     ->where('p.id', '=', $id)
                     ->first();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
