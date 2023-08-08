<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers;

use App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use Illuminate\Support\Facades\App;

class ServiceApplicationClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IServiceApplicationClient::class,App::make($key));
   }

}