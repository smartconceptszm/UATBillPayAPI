<?php

namespace App\Http\Services\Enums;
 
 
enum ClientStatusEnum:string
{
	case Active ='ACTIVE';
	case Blocked ='BLOCKED';
}