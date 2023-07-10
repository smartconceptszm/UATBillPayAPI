<?php

namespace App\Http\BillPay\Services\External\SMSClients;

use App\Http\BillPay\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\App;

class SMSClientBinderService 
{

   public function bind(string $key):void
   {
      App::instance(ISMSClient::class,App::make($key));
   }

}