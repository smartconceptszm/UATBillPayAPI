<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\Auth\APIClientLoginService;
use App\Http\Controllers\Controller;
use App\Http\DTOs\UserLoginDTO;
use Illuminate\Http\Request;

class APIClientLoginController  extends Controller
{

   public function __construct(
		private APIClientLoginService $apiClientLoginService,
      private UserLoginDTO $dto)
	{}

   public function store(Request  $request)
   {

      $this->validate($request, $this->dto->validationRules);
      $this->dto = $this->dto->fromArray($this->getParameters($request));
      $data = $this->apiClientLoginService->create($this->dto);
      return $this->successResponse($data, 201);

   }

}
