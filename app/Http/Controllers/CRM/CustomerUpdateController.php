<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\CustomerFieldUpdateService;
use App\Http\Controllers\Contracts\CRUDController;

class CustomerUpdateController extends CRUDController
{

    protected $validationRules = [
                                'customer_detail_id' => 'required|string',
                                'accountNumber' => 'required|string',
                                'mobileNumber' => 'required|string',
                                'client_id' => 'required|string'
                            ];

    public function __construct(CustomerFieldUpdateService $theService)
    {
        parent::__construct($theService);
    }

}
