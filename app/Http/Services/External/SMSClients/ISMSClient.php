<?php

namespace App\Http\Services\External\SMSClients;

interface ISMSClient 
{

   public function send(array $smsParams): bool;
   
}