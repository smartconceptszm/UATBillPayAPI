<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum PaymentStatusEnum:string
{
    case Initiated ='INITIATED';
    case Submitted ='SUBMITTED';
    case Submission_Failed ='SUBMISSION FAILED';
    case Payment_Failed ='PAYMENT FAILED';
    case Paid ='PAID | NOT RECEIPTED';
    case Receipted ='RECEIPTED';
    case Receipt_Delivered ='RECEIPT DELIVERED';
}