<?php

namespace App\Http\Services\Payments;

use App\Jobs\BatchReceiptDeliveryJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class BatchPaymentReceiptService
{

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         $dto = (object)$data;
         $records = DB::table('payments as p')
                  ->select('id')
                  ->whereIn('p.paymentStatus', ['RECEIPTED'])
                  ->where('p.client_id', '=', $dto->client_id);
         if($dto->dateFrom && $dto->dateTo){
               $records = $records->whereDate('p.created_at', '>=', $dto->dateFrom)
                                 ->whereDate('p.created_at', '<=', $dto->dateTo);
         }
         $records = $records->get();
         $records = $records->all();
         if (\sizeof($records) > 0) {
            $chunkedArr = \array_chunk($records,5,false);
            foreach ($chunkedArr as $value) {
               Queue::later(Carbon::now()->addSeconds(1),
                              new BatchReceiptDeliveryJob($value, $user->urlPrefix));
            }
            return (object)['data' => 'Batch receipt delivery job initiated.\n\n Check status after a few minutes!'];
         } else {
            return (object)['data' => 'No records found!'];
         }
      } catch (Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
