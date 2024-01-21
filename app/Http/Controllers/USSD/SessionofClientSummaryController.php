<?php

namespace App\Http\Controllers\USSD;

use App\Http\Services\USSD\SessionsOfClientSummaryService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionofClientSummaryController extends Controller
{

	public function __construct(
		private SessionsOfClientSummaryService $theService)
	{}

   /**
    * Display a listing of the resource.
   */
   public function index(Request $request)
   {

      try {
         $this->response['data'] =  $this->theService->findAll($request->query());
      } catch (\Throwable $e) {
            $this->response['status']['code'] = 500;
            $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json( $this->response);

   }

}
