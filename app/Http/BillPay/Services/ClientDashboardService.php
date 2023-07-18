<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\ClientDashboardRepo;

class ClientDashboardService implements IFindAllService
{

    private $repository;
    public function __construct(ClientDashboardRepo $repository)
    {
        $this->repository=$repository;
    }

    public function findAll(array $criteria = null, array $fields = ['*']):array|null
    {
        try {
            return $this->repository->findAll();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        
    }

}
