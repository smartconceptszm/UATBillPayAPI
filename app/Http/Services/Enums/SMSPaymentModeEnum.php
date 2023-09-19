<?php

namespace App\Http\Services\Enums;
 
 
enum SMSPaymentModeEnum:string
{
    case PostPaid ='POST-PAID';
    case PrePaid ='PRE-PAID';
}