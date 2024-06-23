<?php

namespace App\Http\Controllers\Web\Auth;


use App\Http\Services\Web\Auth\RightsOfUserService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;

class RightsOfUserController extends Controller
{

	public function __construct(
		private RightsOfUserService $theService)
	{}
                  
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	*/
	public function index(Authenticatable $user){

		try {

			$this->response['data'] = $this->theService->findAll(['user_id' => $user->id]);
		} catch (\Throwable $e) {
			$this->response['status']['code'] = 500;
			$this->response['status']['message'] = $e->getMessage();
		}
		return response()->json( $this->response);
		
	}

}