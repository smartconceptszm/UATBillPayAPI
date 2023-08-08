<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentTransactionRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentTransactionService  implements IFindAllService
{

   protected $repository;
   public function __construct(PaymentTransactionRepo $repository)
   {
      $this->repository=$repository;
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
