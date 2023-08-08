<?php

namespace App\Http\Controllers\Clients;

use App\Http\BillPay\Services\Clients\TopupService;
use App\Http\Controllers\Contracts\CRUDController;

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
