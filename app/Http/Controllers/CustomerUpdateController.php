<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\CustomerUpdateService;
use App\Http\Controllers\CRUDController;

class CustomerUpdateController extends CRUDController
{

    protected $validationRules = [
                                'customer_detail_id' => 'required|string',
                                'accountNumber' => 'required|string',
                                'mobileNumber' => 'required|string',
                                'client_id' => 'required|string'
                            ];

    public function __construct(CustomerUpdateService $theService)
    {
        parent::__construct($theService);
    }

}
