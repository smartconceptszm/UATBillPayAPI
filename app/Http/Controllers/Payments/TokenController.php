<?php

namespace App\Http\Controllers\Payments;

use App\Http\Services\Payments\TokenService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TokenController extends Controller
{

   private $validationRules = [  
                              'id' => 'required|string',
                           ];

	public function __construct(
		private TokenService $tokenService)
	{}

   public function store(Request $request)
   {

      try {
         //validate incoming request 
         $this->validate($request, $this->validationRules);
         $this->response['data'] = $this->tokenService->create($this->getParameters($request));
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
