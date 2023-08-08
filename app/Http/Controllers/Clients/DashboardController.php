<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Contracts\CRUDIndexController;
use App\Http\BillPay\Services\Clients\DashboardService;

class DashboardController extends CRUDIndexController
{

   public function __construct(DashboardService $theService)
   {
      parent::__construct($theService);
   }

}
