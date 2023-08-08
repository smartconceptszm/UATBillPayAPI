<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class BatchPaymentReceiptRepo implements IFindAllRepository
{

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $dto = (object)$criteria;
         $records = DB::table('payments as p')
                  ->select('id')
                  ->whereIn('p.paymentStatus', ['RECEIPTED'])
                  ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
               $records = $records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records = $records->get();
         return $records->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
