<?php

namespace App\Http\Services\USSD\UpdateDetails\ClientCallers;

use App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use Illuminate\Support\Facades\App;

class UpdateDetailsClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IUpdateDetailsClient::class,App::make($key));
   }

}