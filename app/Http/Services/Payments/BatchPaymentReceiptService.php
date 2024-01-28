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
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";
         $records = DB::table('payments as p')
                  ->select('id');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereBetween('p.created_at',[$dto->dateFrom,$dto->dateTo]);
         }
         $records = $records->where('p.client_id', '=', $dto->client_id)
                              ->whereIn('p.paymentStatus', ['RECEIPTED']);
         $records = $records->get()->all();
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
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}
