<?php

namespace App\Http\Services\Enums;
 
 
enum PaymentTypeEnum:string
{
    case PostPaid ='POST-PAID';
    case PrePaid ='PRE-PAID';
    case Other ='OTHER';
}