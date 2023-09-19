<?php

namespace App\Http\Services\Enums;
 
 
enum USSDStatusEnum:string
{
    case Initiated ='INITIATED';
    case Completed ='COMPLETED';
    case Failed ='FAILED';
    case Reviewed ='REVIEWED';
    case Manually_Reviewed ='MANUALLY REVIEWED';
}