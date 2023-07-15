<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Repositories\USSD\SessionOfClientSummaryRepo;
use App\Http\BillPay\Services\Contracts\IFindAllService;
use Exception;

class SessionsOfClientSummaryService implements IFindAllService
{

    private $repository;
    public function __construct(SessionOfClientSummaryRepo $repository)
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
