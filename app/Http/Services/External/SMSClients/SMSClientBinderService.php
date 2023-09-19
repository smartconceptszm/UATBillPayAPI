<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\App;

class SMSClientBinderService 
{

   public function bind(string $key):void
   {
      App::instance(ISMSClient::class,App::make($key));
   }

}