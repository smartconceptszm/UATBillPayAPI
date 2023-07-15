<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\MnoChargeService;
use App\Http\Controllers\Contracts\CRUDController;

class MNOChargeController extends CRUDController
{
    protected $validationRules = [
                'client_id' => 'required|string',
                'mno_id' => 'required|string'
            ];

    public function __construct(MnoChargeService $theService)
    {
        parent::__construct($theService);
    }

}
