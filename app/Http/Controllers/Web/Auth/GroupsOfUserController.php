<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Services\Web\Auth\GroupsOfUserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupsOfUserController extends Controller
{

   public function __construct(
		private GroupsOfUserService $theService)
   {}
                  
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	*/
	public function index(Request  $request,$id){

		try {
			$this->response['data'] = $this->theService->findAll(['user_id' => $id]);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}
