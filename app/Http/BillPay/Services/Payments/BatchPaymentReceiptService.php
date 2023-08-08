<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\BatchPaymentReceiptRepo;
use App\Http\BillPay\Services\Contracts\ICreateService;
use App\Jobs\BatchReceiptDeliveryJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Exception;

class BatchPaymentReceiptService implements ICreateService
{

   private $repository;
   public function __construct(BatchPaymentReceiptRepo $repository)
   {
      $this->repository=$repository;
   }

   public function create(array $data):object|null
   {

      try {
         $user = Auth::user(); 
         $data['client_id'] = $user->client_id;
         $records = $this->repository->findAll($data, ['*']);
         if (\sizeof($records) > 0) {
            $chunkedArr = \array_chunk($records,5,false);
            foreach ($chunkedArr as $value) {
               Queue::later(Carbon::now()->addSeconds(1),
                              new BatchReceiptDeliveryJob($value));
            }
            return (object)['data' => 'Batch receipt delivery job initiated.\n\n Check status after a few minutes!'];
         } else {
            return (object)['data' => 'No records found!'];
         }
      } catch (\Exception $e) {
         throw new Exception($e->getMessage());
      }

   }

}
