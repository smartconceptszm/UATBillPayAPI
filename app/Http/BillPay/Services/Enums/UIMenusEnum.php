<?php

namespace App\Http\BillPay\Services\Enums;
 
 
enum UIMenusEnum:string
{
    case Home ='Home';
    case PayBill ='PayBill';
    case BuyUnits ='BuyUnits';
    case CheckBalance ='CheckBalance';
    case FaultsComplaints ='FaultsComplaints';
    case UpdateDetails ='UpdateDetails';
    case ServiceApplications ='ServiceApplications';
    case OtherPayments ='OtherPayments';
}