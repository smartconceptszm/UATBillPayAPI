<?php

namespace App\Http\Controllers\SMS;

use App\Http\Services\SMS\SMSesOfClientService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSesOfClientController extends Controller
{

	public function __construct(
		private SMSesOfClientService $theService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $params = $request->all();
         $this->response['data'] = $this->theService->findAll($params);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
