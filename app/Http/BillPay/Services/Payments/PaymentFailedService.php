<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentFailedRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class PaymentFailedService implements IFindAllService
{

   private $repository;
   public function __construct(PaymentFailedRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria=null, array $fields = ['*']):array|null{
      try {
         $response=$this->repository->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
         
      }
      return $response;
   }

}
