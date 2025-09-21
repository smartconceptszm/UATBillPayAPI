<?php

namespace App\Http\Controllers\Promotions;

use App\Http\Services\Promotions\RaffleWinnerService;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RaffleWinnerController extends Controller
{

   protected $validationRules = [
      'promotion_id' => 'required',
      'dateOfDraw' => 'required',
      'drawWinner' => 'required',
      'drawNumber' => 'required',
   ];

	public function __construct(
		private RaffleWinnerService $raffleWinnerService)
	{}

   /**
    * Store a newly created resource in storage.
      */
   public function store(Request $request, Authenticatable $user)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $params = $request->all();
         $this->response['data'] = $this->raffleWinnerService->handle($params);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   public function update(Request $request, string $id)
   {

      try {
         $params = $request->all();
         $params['raffleDrawId'] = $id;
         $this->response['data'] = $this->raffleWinnerService->handle($params);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

   
}
