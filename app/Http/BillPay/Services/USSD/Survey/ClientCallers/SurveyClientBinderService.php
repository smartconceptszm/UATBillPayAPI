<?php

namespace App\Http\BillPay\Services\USSD\Survey\ClientCallers;

use App\Http\BillPay\Services\USSD\Survey\ClientCallers\ISurveyClient;
use Illuminate\Support\Facades\App;

class SurveyClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(ISurveyClient::class,App::make($key));
   }

}