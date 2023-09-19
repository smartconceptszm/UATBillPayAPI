<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\App;

class BillingClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IBillingClient::class,App::make($key));
   }

}