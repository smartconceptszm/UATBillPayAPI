<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Repositories\Auth\RightsOfGroupRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class RightsOfGroupService implements IFindAllService
{

   private $repository;
   public function __construct(RightsOfGroupRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null{
      try {
         return  $this->repository->findAll($criteria, $fields);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
         
      }
   }

}
