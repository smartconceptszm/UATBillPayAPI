<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentNotFullyReceiptedRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class PaymentNotFullyReceiptedService implements IFindAllService
{

   private $repository;
   public function __construct(PaymentNotFullyReceiptedRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null{
      try {
         $records=$this->repository->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $records;
   }

}
