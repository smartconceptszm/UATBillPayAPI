<?php

namespace App\Http\Controllers\USSD;

use App\Http\BillPay\Services\USSD\SessionService;
use App\Http\Controllers\Contracts\CRUDController;

class SessionController extends CRUDController
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
