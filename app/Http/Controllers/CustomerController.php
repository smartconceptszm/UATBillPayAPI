<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\CustomerService;
use App\Http\Controllers\CRUDController;

class CustomerController extends CRUDController
{

    protected $validationRules=[
        'accountNumber' => 'required|string',
        'mobileNumber' => 'required|string',
        'client_id' => 'required|string'
    ];
    
    public function __construct(CustomerService $theService)
    {
        parent::__construct($theService);
    }

}
