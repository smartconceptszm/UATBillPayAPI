<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentNotFullyReceiptedRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentNotReceiptedService implements IFindAllService
{

   private $repository;
   public function __construct(PaymentNotFullyReceiptedRepo $repository)
   {
      $this->repository = $repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         return  $this->repository->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
