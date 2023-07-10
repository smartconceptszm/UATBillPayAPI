<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\ComplaintRepo;
use Exception;

class ComplaintService extends BaseService
{

    public function __construct(ComplaintRepo $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data):object|null
    {

        try {
            $data['caseNumber'] = $data['accountNumber'].'_'.date('YmdHis');
            $response = $this->repository->create($data);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $response;
        
    }

}
