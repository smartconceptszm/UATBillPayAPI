<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\PaymentNotFullyReceiptedRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class PaymentNotFullyReceiptedService implements IFindAllService
{

    private $repository;
    public function __construct(PaymentNotFullyReceiptedRepo $repository)
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
