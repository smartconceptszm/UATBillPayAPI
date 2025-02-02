<?php

namespace App\Http\Services\Enums;
 
 
enum USSDStatusEnum:string
{
    case Initiated ='INITIATED';
    case Completed ='COMPLETED';
    case Failed ='FAILED';
    case ClientBlocked ='CLIENTBLOCKED';
    case InvalidAccount  ='INVALIDACCOUNT';
    case InvalidAmount ='INVALIDAMOUNT';
    case InvalidConfirmation  = 'INVALIDCONFIRMATION';
    case InvalidInput  ='INVALIDINPUT';
    case InvalidSurveyResponse  ='INVALIDSURVEYRESPONSE';
    case MaintenanceMode  ='MAINTENANCEMODE';
    case SystemError  = 'SYSTEMERROR';
    case WalletNotActivated  = 'WALLETNOTACTIVATED';
    case Resuming = 'RESUMING';
    case Reviewed ='REVIEWED';
    case ManuallyReviewed ='MANUALLY REVIEWED';
}