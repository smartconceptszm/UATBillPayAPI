<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\External\SMSClients\ISMSClient;

class MockSMSDelivery implements ISMSClient
{

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