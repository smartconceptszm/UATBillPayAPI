<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\UserLoginService;
use App\Http\Controllers\Controller;
use App\Http\DTOs\UserLoginDTO;
use Illuminate\Http\Request;

class UserLoginController  extends Controller
{

   public function __construct(
		private UserLoginService $theService,
      private UserLoginDTO $dto)
	{}

   public function store(Request  $request)
   {

      try {
         $this->validate($request, $this->dto->validationRules);
         $this->dto = $this->dto->fromArray($this->getParameters($request));
         $this->response['data'] = $this->theService->create($this->dto);
      } catch (\Throwable $e) {
         $this->response['status']['code'] = 500;
         $this->response['status']['message'] = $e->getMessage();
      }
      return response()->json($this->response);

   }

}
