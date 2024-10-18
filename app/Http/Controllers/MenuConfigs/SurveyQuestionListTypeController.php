<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\Services\MenuConfigs\SurveyQuestionListTypeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SurveyQuestionListTypeController extends Controller
{

    protected $validationRules = [
									'name' => 'required|string'
								];
	public function __construct(
		private SurveyQuestionListTypeService $surveyQuestionListTypeService)
	{}

	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			$this->response['data'] =  $this->surveyQuestionListTypeService->findAll($request->query());
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
			$this->response['data'] = $this->surveyQuestionListTypeService->create($this->getParameters($request));
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
			$this->response['data'] = $this->surveyQuestionListTypeService->findById($id);
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
			$this->response['data'] = $this->surveyQuestionListTypeService->findOneBy($this->getParameters($request));
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
			$this->response['data'] = $this->surveyQuestionListTypeService->update($this->getParameters($request),$id);
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
			$this->response['data'] = $this->surveyQuestionListTypeService->delete($id);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

}
