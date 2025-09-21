<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\RaffleDrawCompletedService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RaffleDrawCompletedController extends Controller
{

	public function __construct(
		private RaffleDrawCompletedService $raffleDrawCompletedService)
	{}

   /**
    * Display a listing of the resource.
   */
  public function index(Request $request)
  {

     try { 
        $params = $request->all();
        $this->response['data'] =  $this->raffleDrawCompletedService->handle($params);
     } catch (\Throwable $e) {
           $this->response['status']['code'] = 500;
           $this->response['status']['message'] = $e->getMessage();
     }
     return response()->json( $this->response);

  }

   
}
