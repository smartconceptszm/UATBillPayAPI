<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientService;
use App\Http\Controllers\Contracts\CRUDController;

class ClientController extends CRUDController
{

    protected  $validationRules=[
                                'code' => 'required|string|unique:clients',
                                'name' => 'required|string|unique:clients',
                                'shortName' => 'required|string|unique:clients',
                                'urlPrefix' => 'required|string|unique:clients'
                            ];
    public function __construct(ClientService $theService)
    {
        parent::__construct($theService);
    }

}
