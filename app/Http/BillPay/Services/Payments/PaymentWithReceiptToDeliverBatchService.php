<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentWithReceiptToDeliverBatchRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Jobs\BatchReceiptDeliveryJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;
use Exception;

class PaymentWithReceiptToDeliverBatchService implements IFindAllService
{

   private $repository;
   public function __construct(PaymentWithReceiptToDeliverBatchRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null{
      try {
         $records=$this->repository->findAll($criteria, $fields);
         if (\sizeof($records) > 0) {
               $chunkedArr=\array_chunk($records,5,false);
               foreach ($chunkedArr as $value) {
                  Queue::later(Carbon::now()->addSeconds(3),
                                 new BatchReceiptDeliveryJob($value));
               }
         } 
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records;
   }

}
