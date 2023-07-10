<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ComplaintService;
use App\Http\Controllers\CRUDController;

class ComplaintController extends CRUDController
{

    protected $validationRules = [
                                'complaint_subtype_id' => 'required|string',
                                'accountNumber' => 'required|string',
                                'mobileNumber' => 'required|string',
                                'client_id' => 'required|string'
                            ];

    public function __construct(ComplaintService $theService)
    {
        parent::__construct($theService);
    }

}
