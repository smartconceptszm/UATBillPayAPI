<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum PaymentChannelEnum:string
{
    case Ussd ='USSD';
    case Mobile ='MOBILEAPP';
    case Bank ='BANK';
    case Bank ='WEBAPP';
    case Wbsite ='WEBSITE';
}