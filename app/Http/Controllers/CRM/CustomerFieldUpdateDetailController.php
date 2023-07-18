<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\CustomerFieldUpdateDetailService;
use App\Http\Controllers\Contracts\CRUDController;

class CustomerFieldUpdateDetailController extends CRUDController
{

   protected $validationRules=[
      'customer_field_update_id' => 'required|string',
      'customer_field_id' => 'required|string',
      'value' => 'required|string'
   ];

   public function __construct(CustomerFieldUpdateDetailService $theService)
   {
      parent::__construct($theService);
   }

}
