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
         $records = DB::table('payments as p')
            ->select('id', 'error')
            ->whereIn('p.paymentStatus', ['SUBMITTED','SUBMISSION FAILED','PAYMENT FAILED'])
            ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
            $records =$records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records =$records->get();
         $providerErrors = $records->filter(
               function ($item) {
                  if ((\strpos($item->error,"Status Code"))
                        || (\strpos($item->error,"on get transaction status"))
                        || (\strpos($item->error,"Get Token error"))
                        || (\strpos($item->error,"on collect funds"))) 
                  {
                        return $item;
                  }
               }
            )->values();
         return $providerErrors->all();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      } 

   }

   public function findById(string $id):object|null{

      try {
         return DB::table('payments as p')
            ->join('sessions as s','p.session_id','=','s.id')
            ->join('mnos as m','s.mno_id','=','m.id')
            ->join('clients as c','s.client_id','=','c.id')
            ->select('p.*','s.sessionId','s.customerJourney',
                        'c.code as clientCode','c.urlPrefix',
                                 'm.name as mnoName')
            ->where('p.id', '=', $id)
            ->first();
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
