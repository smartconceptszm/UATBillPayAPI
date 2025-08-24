<?php

namespace App\Http\Controllers\Bank;

use App\Http\Services\Auth\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{

   protected $validationRules=[
         'mobileNumber' => 'required|string|size:12|unique:users',
         'username' => 'required|string|unique:users',
         'client_id' => 'required|string',
         'fullnames' => 'required|string',
         'password' => 'required|string',
			'email' => 'email:rfc,dns'
      ];

	public function __construct(
		private UserService $theService)
	{}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{

		try {
			//validate incoming request 
			$userData = $this->validate($request, $this->validationRules);
			$this->response['data'] = $this->theService->create($userData);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

	/**
	 * Display the specified resource.
	 */
	public function show(Request $request, string $id)
	{

		try {
			$this->response['data'] = $this->theService->findById($id);
		} catch (\Throwable $e) {
				$this->response['status']['code'] = 500;
				$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{

		try {
			$this->response['data'] = $this->theService->update($this->getParameters($request),$id);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}


}