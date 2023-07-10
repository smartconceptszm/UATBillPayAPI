<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\IFindAllService;
use App\Http\BillPay\Repositories\PaymentToClientRepo;
use Exception;

class PaymentToClientService  implements IFindAllService
{

    private $repository;
    public function __construct(PaymentToClientRepo $repository)
    {
        $this->repository=$repository;
    }

    public function findAll(array $criteria = null):array|null{
        try {
            $records=$this->repository->findAll($criteria);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
        return $records;
    }

}
