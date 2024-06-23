<?php

namespace App\Http\Controllers\Web\CRM;

use App\Http\Services\Web\CRM\ServiceApplicationDetailService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceApplicationDetailController extends Controller
{

	protected $validationRules = [
								'service_application_id' => 'required',
								'service_type_detail_id' => 'required',
								'value' => 'required|string',
							];
	public function __construct(
		private ServiceApplicationDetailService $serviceApplicationDetailService)
	{}

	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			$this->response['data'] =  $this->serviceApplicationDetailService->findAll($request->query());
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
			$this->response['data'] = $this->serviceApplicationDetailService->create($this->getParameters($request));
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
			$this->response['data'] = $this->serviceApplicationDetailService->findById($id);
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
			$this->response['data'] = $this->serviceApplicationDetailService->findOneBy($this->getParameters($request));
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
			$this->response['data'] = $this->serviceApplicationDetailService->update($this->getParameters($request),$id);
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
			$this->response['data'] = $this->serviceApplicationDetailService->delete($id);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

}
