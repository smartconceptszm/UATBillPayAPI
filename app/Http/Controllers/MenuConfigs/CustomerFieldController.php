<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\Services\MenuConfigs\CustomerFieldService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerFieldController extends Controller
{

	protected $validationRules = [
											'client_id' => 'required|string',
											'name' => 'required|string',
											'order' => 'required',
											'prompt' => 'required'
										];

	public function __construct(
		private CustomerFieldService $customerFieldService)
	{}

	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			$this->response['data'] =  $this->customerFieldService->findAll($request->query());
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
			$this->response['data'] = $this->customerFieldService->create($this->getParameters($request));
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
			$this->response['data'] = $this->customerFieldService->findById($id);
		} catch (\Throwable $e) {
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
			$this->response['data'] = $this->customerFieldService->findOneBy($this->getParameters($request));
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

	/**
	* Display the specified resources.
	*/
	public function customerfieldsofclient(Request $request, string $id)
	{

		try {
			$this->response['data'] = $this->customerFieldService->findAll(['client_id'=>$id]);
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
			$this->response['data'] = $this->customerFieldService->update($this->getParameters($request),$id);
		} catch (\Throwable $e) {
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
			$this->response['data'] = $this->customerFieldService->delete($id);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

}
