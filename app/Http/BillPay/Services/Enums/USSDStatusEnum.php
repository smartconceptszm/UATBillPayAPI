<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum USSDStatusEnum:string
{
    case Initiated ='INITIATED';
    case Completed ='COMPLETED';
    case Failed ='FAILED';
    case Reviewed ='REVIEWED';
    case Manually_Reviewed ='MANUALLY REVIEWED';
}