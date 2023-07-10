<?php

namespace App\Http\BillPay\Services\External\MoMoClients;

use App\Http\BillPay\Services\External\MoMoClients\IMoMoClient;
use Illuminate\Support\Facades\App;

class MoMoClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IMoMoClient::class,App::make($key));
   }

}