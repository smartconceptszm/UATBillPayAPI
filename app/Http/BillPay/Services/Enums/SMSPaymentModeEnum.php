<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum SMSPaymentModeEnum:string
{
    case PostPaid ='POST-PAID';
    case PrePaid ='PRE-PAID';
}