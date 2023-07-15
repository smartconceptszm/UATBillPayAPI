<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\ComplaintService;
use App\Http\Controllers\Contracts\CRUDController;

class ComplaintController extends CRUDController
{

   protected $validationRules = [
                              'complaint_subtype_id' => 'required|string',
                              'accountNumber' => 'required|string',
                              'mobileNumber' => 'required|string',
                              'client_id' => 'required|string'
                           ];

   public function __construct(ComplaintService $theService)
   {
      parent::__construct($theService);
   }

}
