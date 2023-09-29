<?php

return [
	'chambeshi'=>[
			'shortCode'=>'2220',
			'hasPrepaid'=>true,
			'prepaidExample'=>'',
			'postpaidExample'=>'',
			'hasUpdateDetailsSubMenu'=>true,
			'hasOwnSMS'=>false,
			'hasOwnComplaintsSys'=>false
		],
	'lukanga'=>[
			'shortCode'=>'2106',
			'hasPrepaid'=>false,
			'prepaidExample'=>'',
			'postpaidExample'=>'',
			'hasOwnSMS'=>false,
			'hasOwnComplaintsSys'=>false
		],
	'swasco'=>[
				'shortCode'=>'5757',
				'hasPrepaid'=>false,
				'prepaidExample'=>'',
				'postpaidExample'=>'liv000xxxx or cho000xxxx',
				'hasOwnSMS'=>true,
				'sms_Base_URL'=>'https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/',
				'sms_APIKEY' => 'fb122bb2fca7703d8eed1d239e3f349a',
				'sms_SENDER_ID' => 'SWSC',
				'remote_Timeout'=>'20', //in seconds
				'receipting_Timeout'=>'40', //in seconds
			],

];
