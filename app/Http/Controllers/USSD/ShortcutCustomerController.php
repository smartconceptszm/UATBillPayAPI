<?php

namespace App\Http\Controllers\USSD;

use App\Http\BillPay\Services\USSD\ShortcutCustomerService;
use App\Http\Controllers\Contracts\CRUDController;

class ShortcutCustomerController extends CRUDController
{

   protected $validationRules=[
      'accountNumber' => 'required|string',
      'mobileNumber' => 'required|string',
      'client_id' => 'required|string'
   ];
   
   public function __construct(ShortcutCustomerService $theService)
   {
      parent::__construct($theService);
   }

}
