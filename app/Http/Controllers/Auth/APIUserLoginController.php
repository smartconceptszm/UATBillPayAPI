<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\APIUserLoginService;
use App\Http\Controllers\Controller;
use App\Http\DTOs\UserLoginDTO;
use Illuminate\Http\Request;

class APIUserLoginController  extends Controller
{

   public function __construct(
		private APIUserLoginService $apiUserLoginService,
      private UserLoginDTO $dto)
	{}

   public function store(Request  $request)
   {

      $this->validate($request, $this->dto->validationRules);
      $this->dto = $this->dto->fromArray($this->getParameters($request));
      $data = $this->apiUserLoginService->create($this->dto);
      return $this->successResponse($data, 201);

   }

}
