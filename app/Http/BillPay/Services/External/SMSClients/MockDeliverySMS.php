<?php

namespace App\Http\BillPay\Services\External\SMSClients;

use App\Http\BillPay\Services\External\SMSClients\ISMSClient;

class MockDeliverySMS implements ISMSClient
{
    
    public function channelChargeable():bool
    {
        return false;
    }

    /**
     * Send sms message.
     *
     * @param  Array  $smsParams['mobileNumber'=>'','message'=>'','clientShortName'=>'']
     * @return Bool 
     */
    public function send(Array $smsParams): bool
    {
        return true;
    }

}