<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\APIUsersOfAPIClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIUsersOfAPIClientController extends Controller
{

	public function __construct(
		private APIUsersOfAPIClientService $theService)
	{}
                  
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index(string $id){

      try {
         $this->response['data'] =  $this->theService->findAll(['api_client_id' => $id]);
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);
      
   }

}