<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\GroupsOfUserRepo;
use Exception;

class GroupsOfUserService implements IFindAllService
{

    private $repository;
    public function __construct(GroupsOfUserRepo $repository)
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
