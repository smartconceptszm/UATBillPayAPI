<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\Auth\GroupRepo;

class GroupService extends BaseService
{

   public function __construct(GroupRepo $repository)
   {
      parent::__construct($repository);
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null{
      try {
         $response=$this->repository->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

}
