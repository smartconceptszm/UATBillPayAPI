<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SessionService;
use App\Http\Controllers\CRUDController;

class SessionsController extends CRUDController
{

    protected $validationRules=[
        'customerJourney' => 'required|string',
        'mobileNumber' => 'required|string',
        'sessionId' => 'required|string',
        'client_id' => 'required|string'
    ];

    public function __construct(SessionService $theService)
    {
        parent::__construct($theService);
    }
    

}
