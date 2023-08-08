<?php

namespace App\Http\Controllers\Clients;

use App\Http\BillPay\Services\Clients\MnoService;
use App\Http\Controllers\Contracts\CRUDController;

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
