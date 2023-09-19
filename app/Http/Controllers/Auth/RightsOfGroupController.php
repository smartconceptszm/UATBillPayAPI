<?php

namespace App\Http\Controllers\Auth;


use App\Http\Services\Auth\RightsOfGroupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RightsOfGroupController extends Controller
{

	public function __construct(
		private RightsOfGroupService $theService)
	{}
                  
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	*/
	public function index(Request  $request,$id){

		try {
			$this->response['data'] = $this->theService->findAll(['group_id' => $id]);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}