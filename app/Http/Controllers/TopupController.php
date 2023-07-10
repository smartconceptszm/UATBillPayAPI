<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\TopupService;
use App\Http\Controllers\CRUDController;

class TopupController extends CRUDController
{

    protected $validationRules=[
        'client_id' => 'required|string',
        'amount' => 'required|string'
    ];
    public function __construct(TopupService $theService)
    {
        parent::__construct($theService);
    }

}
