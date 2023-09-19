<?php

namespace App\Http\Services\Utility;

use Psr\Http\Message\MessageInterface;
use GuzzleHttp\Psr7\Message;

class PsrMessageToStringConvertor
{

	public function toString(MessageInterface $message): string
	{
		return Message::toString($message);
	}

}