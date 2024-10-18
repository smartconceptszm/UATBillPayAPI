<?php

namespace App\Http\Controllers\Sessions;

use App\Http\Services\Sessions\SessionsOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionofClientController extends Controller
{

	public function __construct(
		private SessionsOfClientService $sessionsOfClientService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->sessionsOfClientService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
