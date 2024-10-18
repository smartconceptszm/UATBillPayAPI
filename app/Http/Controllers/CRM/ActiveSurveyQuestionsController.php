<?php

namespace App\Http\Controllers\CRM;

use App\Http\Services\CRM\ActiveSurveyQuestionsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActiveSurveyQuestionsController extends Controller
{

	public function __construct(
		private ActiveSurveyQuestionsService $activeSurveyQuestionsService)
	{}
                  
	/**
		* Display a listing of the resource.
		*
		* @return \Illuminate\Http\Response
	*/
	public function index(Request  $request){

		try {
			$this->response['data'] =  $this->activeSurveyQuestionsService->findAll($request->query());
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}
