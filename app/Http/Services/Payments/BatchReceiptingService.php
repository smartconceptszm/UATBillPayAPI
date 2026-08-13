<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use App\Jobs\BatchReceiptingJob;
use Illuminate\Support\Carbon;
use Exception;

class BatchReceiptingService
{

   public function create(array $data):object|null
   {

      try {

         $dto = (object)$data;
         $dto->dateFrom = $dto->dateFrom." 00:00:00";
         $dto->dateTo = $dto->dateTo." 23:59:59";

         $theDate = Carbon::parse($dto->dateTo);
         $dateTo = $theDate->copy()->startOfDay();
         $today = Carbon::now()->startOfDay();

      
         if ($data['receiptingType'] == 'FORCECLOSEBATCH'){
            if ($dateTo->gte($today)) {
               return (object)['data' => 'CAN NOT force closed batch for today or future date!'];
            }
         }
         
         $records = DB::table('payments as p')
                              ->join('client_wallets as cw','p.wallet_id','=','cw.id')
                              ->select('p.id');
         if($dto->dateFrom && $dto->dateTo){
            $records = $records->whereBetween('p.created_at',[$dto->dateFrom,$dto->dateTo]);
         }
         $records = $records->where('cw.client_id', '=', $dto->client_id)
                              ->where('p.paymentStatus','=', PaymentStatusEnum::Paid->value);
         $records = $records->pluck('id')
                           ->toArray();

         if (\sizeof($records) > 0) {
            $chunkedArr = \array_chunk($records,5,false);
            foreach ($chunkedArr as $subChunk) {
               $receiptData = [];
               foreach ($subChunk as  $value) {
                  array_push($receiptData,[
                        'receiptingType' => $data['receiptingType'],
                        'id' => $value
                     ]);
               }
               BatchReceiptingJob::dispatch($receiptData)
                                             ->delay(Carbon::now()->addSeconds(1))
                                             ->onQueue('low');
            }
            return (object)['data' => 'Batch receipting process initiated. Check status after a few minutes!'];
         } else {
            return (object)['data' => 'No records found!'];
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}