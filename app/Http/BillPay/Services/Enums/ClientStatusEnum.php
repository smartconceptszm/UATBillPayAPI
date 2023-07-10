<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum ClientStatusEnum:string
{
    case Active ='ACTIVE';
    case Blocked ='BLOCKED';
}