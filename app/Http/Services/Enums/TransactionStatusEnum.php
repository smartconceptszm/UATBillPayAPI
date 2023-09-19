<?php

namespace App\Http\Services\Enums;
 
 
enum TransactionStatusEnum:string
{
    case Initiated ='INITIATED';
    case Completed ='COMPLETED';
    case Failed ='FAILED';
    case Reviewed ='REVIEWED';
    case Manually_Reviewed ='MANUALLY REVIEWED';
}