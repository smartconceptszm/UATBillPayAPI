<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Services\Web\Auth\GroupsOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupsOfClientController extends Controller
{

	public function __construct(
		private GroupsOfClientService $theService)
	{}
                  
	/**
		* Display a listing of the resource.
		*
		* @return \Illuminate\Http\Response
		*/
	public function index(Request  $request,$id){

		try {
			$this->response['data'] = $this->theService->findAll(['client_id' => $id]);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}
