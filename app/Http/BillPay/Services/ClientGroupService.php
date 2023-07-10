<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\ClientGroupRepo;
use Exception;

class ClientGroupService implements IFindAllService
{

    private $repository;
    public function __construct(ClientGroupRepo $repository)
    {
        $this->repository=$repository;
    }

    public function findAll(array $criteria=null):array|null{
        try {
            $response=$this->repository->findAll($criteria);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
            
        }
        return $response;
    }

}
