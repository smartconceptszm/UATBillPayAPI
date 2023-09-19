<?php
namespace App\Http\Services\Enums;

class MNOs 
{

	public static function getMNO(String $code)
	{

		$mnoNames=[
			'26095'=>'ZAMTEL',
			'26075'=>'ZAMTEL',
			'26096'=>'MTN',
			'26076'=>'MTN',
			'26097'=>'AIRTEL',
			'26077'=>'AIRTEL',
		];
		return $mnoNames[$code];
		
	}

}
