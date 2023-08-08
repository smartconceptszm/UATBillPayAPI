<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentService;
use App\Http\Controllers\Contracts\CRUDController;

class PaymentController extends CRUDController
{

   protected $validationRules = [
                  'accountNumber' => 'required|string',
                  'mobileNumber' => 'required|string',
                  'session_id' => 'required|string',
                  'client_id' => 'required|string'
               ];
   public function __construct(PaymentService $theService)
   {
      parent::__construct($theService);
   }

}
