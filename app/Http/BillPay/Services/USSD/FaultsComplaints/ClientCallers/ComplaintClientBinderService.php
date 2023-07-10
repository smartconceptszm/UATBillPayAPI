<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers;

use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use Illuminate\Support\Facades\App;

class ComplaintClientBinderService 
{

   public function bind(String $key):void
   {
      App::instance(IComplaintClient::class,App::make($key));
   }

}