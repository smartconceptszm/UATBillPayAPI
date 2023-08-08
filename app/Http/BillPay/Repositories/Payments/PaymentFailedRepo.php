<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\IFindAllRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentFailedRepo implements IFindAllRepository
{

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $dto=(object)$criteria;
         $records = DB::table('payments as p')
                  ->join('mnos as m','p.mno_id','=','m.id')
                  ->select('p.id','p.created_at','p.mobileNumber','p.accountNumber','p.receiptNumber',
                           'p.receiptAmount','p.paymentAmount','p.transactionId','p.district',
                           'p.mnoTransactionId','m.name as mno','p.paymentStatus','p.error')
                  ->whereIn('p.paymentStatus', ['SUBMISSION FAILED','PAYMENT FAILED'])
                  ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
               $records =$records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $allFailed = $records->get();
         $providerErrors = $allFailed->filter(
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
         return [
                     'all' => $allFailed,
                     'providerErrors' => $providerErrors,
                  ];
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      } 

   }

}
