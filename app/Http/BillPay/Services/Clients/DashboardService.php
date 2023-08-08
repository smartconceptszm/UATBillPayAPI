<?php

namespace App\Http\BillPay\Services\Clients;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\Clients\DashboardRepo;
use Exception;

class DashboardService implements IFindAllService
{

   private $repository;
   public function __construct(DashboardRepo $repository)
   {
      $this->repository = $repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {

      try {
         $response = $this->repository->findAll($criteria);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;

   }

}
