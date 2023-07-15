<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\ServiceTypeService;
use App\Http\Controllers\Contracts\CRUDController;

class ServiceTypeController extends CRUDController
{
   protected $validationRules = [
                                 'name' => 'required|string'
                              ];

   public function __construct(ServiceTypeService $theService)
   {
      parent::__construct($theService);
   }
}
