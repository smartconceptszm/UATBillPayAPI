<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientOtherPaymentTypeViewRepo;
use App\Http\BillPay\Services\Contracts\IFindOneByService;
use App\Http\BillPay\Services\Contracts\IFindByIdService;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class ClientOtherPaymentTypeViewService implements IFindOneByService,
                                            IFindByIdService, IFindAllService
{

    private $repository;
    public function __construct(ClientOtherPaymentTypeViewRepo $repository)
    {
        $this->repository=$repository;
    }

    public function findAll(array $criteria = null):array|null
    {
        try {
            $response=$this->repository->findAll($criteria);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        return $response;
    }

    public function findById(string $id):object|null
    {

        try {
            $response=$this->repository->findById($id);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
            
        }
        return $response;

    }

    public function findOneBy(array $criteria):object|null
    {
        try {
            $response=$this->repository->findOneBy($criteria);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
            
        }
        return $response;
    }

}
