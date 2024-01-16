<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class UserController extends Controller
{

   protected $validationRules=[
         'mobileNumber' => 'required|string|size:12|unique:users',
         'username' => 'required|string|unique:users',
         'client_id' => 'required',
         'fullnames' => 'required|string',
         'password' => 'required|string',
      ];

	public function __construct(
		private UserService $theService)
	{}


	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			// Log::info('(EFECTIVO querry params)  '.\json_encode($request->query()));
			$this->response['data'] = $this->theService->findAll($request->query());
		} catch (\Throwable $e) {
				$this->response['status']['code'] = 500;
				$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);

	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{

		try {
			//validate incoming request 
			$this->validate($request, $this->validationRules);
			$this->response['data'] = $this->theService->create($request->all());
		} catch (\Exception $e) {
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
		} catch (\Exception $e) {
				$this->response['status']['code'] = 500;
				$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

	/**
	 * Display one resource.
	 */
	public function findOneBy(Request $request)
	{

		try {
			$this->response['data'] = $this->theService->findOneBy($request->all());
		} catch (\Exception $e) {
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

			$this->response['data'] = $this->theService->update($request->all(),$id);
		} catch (\Exception $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}


}