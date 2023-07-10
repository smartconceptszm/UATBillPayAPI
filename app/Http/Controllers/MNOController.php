<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\MnoService;
use App\Http\Controllers\CRUDController;

class MNOController extends CRUDController
{
    public $validationRules = [
        'name' => 'required|string',
        'colour' => 'required|string'
    ];
    public function __construct(MnoService $theService)
    {
        parent::__construct($theService);
    }

}
