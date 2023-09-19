<?php

namespace App\Http\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use Illuminate\Support\Facades\App;

class ComplaintClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IComplaintClient::class,App::make($key));
   }

}