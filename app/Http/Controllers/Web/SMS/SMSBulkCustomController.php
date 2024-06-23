<?php

namespace App\Http\Controllers\Web\SMS;

use App\Http\Services\Web\SMS\SMSMessageBulkCustomService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSBulkCustomController extends Controller
{

	private $validationRules=[
		'mobileNumbers' => 'required',
		'client_id' => 'required'
	];

	public function __construct(
		private SMSMessageBulkCustomService $smsMessageBulkCustomService)
	{}
											
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request  $request)
	{

		try {
			//validate incoming request 
			$this->validate($request, $this->validationRules);
			$this->response['data'] = $this->smsMessageBulkCustomService->create($this->getParameters($request));

		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}      

}
