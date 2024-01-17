<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\SMSMessageBulkCustomService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSBulkCustomController extends Controller
{

	private $validationRules=[
		'mobileNumbers' => 'required',
		'client_id' => 'required'
	];

	public function __construct(
		private SMSMessageBulkCustomService $theService)
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
			$this->response['data'] = $this->theService->create($request->all());

		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}      

}
