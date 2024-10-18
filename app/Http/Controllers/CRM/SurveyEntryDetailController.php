<?php

namespace App\Http\Controllers\CRM;

use App\Http\Services\CRM\SurveyEntryDetailService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SurveyEntryDetailController extends Controller
{

   protected $validationRules=[
      'survey_entry_id' => 'required',
      'survey_question_id' => 'required',
      'answer' => 'required|string'
   ];
	public function __construct(
		private SurveyEntryDetailService $surveyEntryDetailService)
	{}

	/**
	 * Display a listing of the resource.
	*/
	public function index(Request $request)
	{

		try {
			$this->response['data'] =  $this->surveyEntryDetailService->findAll($request->query());
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
			$this->response['data'] = $this->surveyEntryDetailService->create($this->getParameters($request));
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
			$this->response['data'] = $this->surveyEntryDetailService->findById($id);
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
			$this->response['data'] = $this->surveyEntryDetailService->findOneBy($this->getParameters($request));
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
			$this->response['data'] = $this->surveyEntryDetailService->update($this->getParameters($request),$id);
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
			$this->response['data'] = $this->surveyEntryDetailService->delete($id);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json($this->response);

	}

}
