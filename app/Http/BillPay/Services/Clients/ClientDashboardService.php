<?php

namespace App\Http\BillPay\Services\Clients;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\Clients\DashboardRepo;
use Illuminate\Support\Facades\Auth;

class ClientDashboardService implements IFindAllService
{

   private $repository;
   public function __construct(DashboardRepo $repository)
   {
      $this->repository=$repository;
   }

   public function findAll(array $criteria = null, array $fields = ['*']):array|null
   {
      
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         return $this->repository->findAll($criteria);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
