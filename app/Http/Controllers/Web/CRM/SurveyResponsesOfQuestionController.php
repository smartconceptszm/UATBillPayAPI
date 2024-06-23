<?php

namespace App\Http\Controllers\Web\CRM;

use App\Http\Services\Web\CRM\SurveyResponsesOfQuestionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SurveyResponsesOfQuestionController extends Controller
{

	public function __construct(
		private SurveyResponsesOfQuestionService $surveyResponsesOfQuestionService)
	{}
                  
	/**
		* Display a listing of the resource.
		*
		* @return \Illuminate\Http\Response
	*/
	public function index(Request  $request){

		try {
			$this->response['data'] =  $this->surveyResponsesOfQuestionService->findAll($request->query());
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}
