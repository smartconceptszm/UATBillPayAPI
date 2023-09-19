<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Auth\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
	
	private $validationRules = [
			'name' => 'required|string|unique:groups'
		];
	public function __construct(
		private GroupService $theService)
	{}

	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			$this->response['data'] = $this->theService->findAll($request->all());
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

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		
		try {
			$this->response['data'] = $this->theService->delete($id);
		} catch (\Exception $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}
    
}
