<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\UsersOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersOfClientController extends Controller
{

	public function __construct(
		private UsersOfClientService $theService)
	{}
                  
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request  $request){

      try {
         $this->response['data'] = $this->theService->findAll($request->all());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);
      
   }

}