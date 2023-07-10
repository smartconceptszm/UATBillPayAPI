<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\BillPay\Services\CustomerDetailService;
use Exception;

class UpdateDetails_Local implements IUpdateDetailsClient
{
    private $customerUpdateService;
    public function __construct(CustomerDetailService $customerUpdateService)
    {
       $this->customerUpdateService = $customerUpdateService;
    }

    public function create(array $customerDetailsData):string
    {
        try{
            $customerDetails = $this->customerUpdateService->create($customerDetailsData);
            return $customerDetails->caseNumber;  
        } catch (\Throwable $e) {
            throw new Exception('At Post customer update details. '.$e->getMessage());
        }                                             

    }

}