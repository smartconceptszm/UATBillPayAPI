<?php

namespace App\Http\Services\Enums;
 
 
enum PaymentChannelEnum:string
{
    case Ussd ='USSD';
    case Mobile ='MOBILEAPP';
    case Bank ='BANK';
    case WebApp ='WEBAPP';
    case Wbsite ='WEBSITE';
}