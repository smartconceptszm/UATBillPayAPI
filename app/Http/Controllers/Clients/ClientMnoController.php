<?php

namespace App\Http\Controllers\Clients;

use App\Http\BillPay\Services\Clients\ClientMnoService;
use App\Http\Controllers\Contracts\CRUDController;

class ClientMnoController extends CRUDController
{
   
   protected $validationRules = [
               'client_id' => 'required|string',
               'mno_id' => 'required|string'
         ];

   public function __construct(ClientMnoService $theService)
   {
      parent::__construct($theService);
   }

}
