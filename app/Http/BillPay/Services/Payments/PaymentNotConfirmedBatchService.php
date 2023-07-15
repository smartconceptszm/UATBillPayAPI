<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentToReviewBatchRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Jobs\BatchReConfirmPaymentJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;
use Exception;

class PaymentNotConfirmedBatchService implements IFindAllService
{

   private $repository;
   public function __construct(PaymentToReviewBatchRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null{
      try {
         $records=$this->repository->findAll($criteria,$fields);
         if (\sizeof($records) > 0) {
               $chunkedArr=\array_chunk($records,5,false);
               foreach ($chunkedArr as $value) {
                  Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                                 new BatchReConfirmPaymentJob($value));
               }
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records;
   }

}
