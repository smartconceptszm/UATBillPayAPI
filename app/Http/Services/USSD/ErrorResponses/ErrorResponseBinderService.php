<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\App;

class ErrorResponseBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IErrorResponse::class,App::make($key));
   }

}