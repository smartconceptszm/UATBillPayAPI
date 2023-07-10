<?php

namespace App\Http\BillPay\Services\Utility;

use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\MessageInterface;

class PsrMessageToStringConvertor
{

    public function toString(MessageInterface $message): string
    {
        return Message::toString($message);
    }

}